<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Form\AnnulationFormType;
use App\Form\model\AnnulationFormModel;
use App\Form\model\RechercheFormModel;
use App\Form\RechercheFormType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Service\EtatManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sorties_liste", methods={"GET", "POST"})
     */
    public function liste(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, Request $request, EtatManager $etatManager): Response
    {
        $auto = $etatManager->modificationAutomatiqueEtats();
        dump($auto);
        $user = $this->getUser();
        $campus = $this->getUser()->getCampus();
        $rechercheModel = new RechercheFormModel();
        $rechercheModel->setCampus($campus);

        $form = $this->createForm(RechercheFormType::class, $rechercheModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sorties = $sortieRepository->findByFormulaire($rechercheModel, $this->getUser());
        }else{
            $sorties = $sortieRepository->findByFormulaire($rechercheModel, $this->getUser());
        }


        return $this->render('sortie/index.html.twig', [
            'RechercheForm' => $form->createView(),
            'sorties' => $sorties,
        ]);
    }



    /**
     * @Route("/NouvelleSortie", name="sortie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SortieRepository $sortieRepository, EtatManager $etatManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        $sortie->setOrganisateur($this->getUser());
        dump($sortie);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('enregistrer')->isClicked())
                $sortie->setEtat($etatManager->recupererEtats('EN CREATION'));

            else if ($form->get('publier')->isClicked())
                $sortie->setEtat($etatManager->recupererEtats('OUVERTE'));
            //todo: Un else() qui envoie un message d'erreur et redirige vers la page principale, ce serait juste pour faire propre dans le code.


            $sortie->setOrganisateur($this->getUser());
            $sortie->addParticipant($this->getUser());
            $sortieRepository->add($sortie, true);

            $this->addFlash('success', 'Sortie créée !');
            return $this->redirectToRoute('sorties_liste', [], Response::HTTP_SEE_OTHER);
            //todo: éventuellement, rediriger vers "Modifier la sortie" de la nouvelle sortie ('app_sortie_edit', ['id' => $sortie->getId()]

        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/sortie/{id}", name="app_sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/sortie/{id}/modifier", name="app_sortie_edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
     */
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository, EtatManager $etatManager, $id): Response
    {
        $etatEnCreation = $etatManager->recupererEtats('EN CREATION');
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        $sortie = $sortieRepository->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($sortie->getEtat() === $etatEnCreation) {
            $sortieRepository->add($sortie, true);
            $this->addFlash('success', 'La sortie a bien été modifiée !');
            }
            else {
                $this->addFlash('error', 'La modification de la sortie a échoué ! Si le problème persiste, contactez un administrateur.');
            }

            return $this->redirectToRoute('sorties_liste', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/sortie/{id}/supprimer", name="app_sortie_delete", methods={"POST"})
     */
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('sorties_liste', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route ("/inscription/{id}", name="sortie_inscription", requirements={"id": "\d+"})
     */
    public function inscriptionSortie(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatManager $etatManager, $id)
    {

        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();
        $etatOuverte = $etatManager->recupererEtats('OUVERTE');
        $etatCloturee = $etatManager->recupererEtats('CLOTUREE');

        if ( $sortie->getEtat() === $etatOuverte ) {
            $sortie->addParticipant($user);
            $NbParticipants = count($sortie->getParticipants());
            if($NbParticipants >= $sortie->getNbInscriptionsMax()) {
                $sortie->setEtat($etatCloturee);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Votre inscription a bien été enregistrée ! ');
        } else {
            $this->addFlash('error', 'Inscription impossible');
        }

        return $this->redirectToRoute('sorties_liste');
    }

    /**
     * @Route ("/desinscription/{id}", name="sortie_desinscription", requirements={"id": "\d+"})
     */
    public function desinscriptionSortie(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatManager $etatManager, $id)
    {
        $etatOuverte = $etatManager->recupererEtats('OUVERTE');
        $etatCloturee = $etatManager->recupererEtats('CLOTUREE');
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();

        if ( $sortie->getEtat() === $etatOuverte or $sortie->getEtat() === $etatCloturee ) {
            $sortie->removeParticipant($user);
            $NbParticipants = count($sortie->getParticipants());
            if($NbParticipants < $sortie->getNbInscriptionsMax() and $sortie->getDateLimiteInscription() >= 'now') {
                $sortie->setEtat($etatOuverte);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Votre désinscription a bien été prise en compte !');
        } else {
            $this->addFlash('error', 'Erreur : votre désinscription a échouée ! Veuillez contacter un administrateur');
        }

        return $this->redirectToRoute('sorties_liste');
    }

    /**
     * @Route ("/annulerSortie/{id}", name="sortie_annulation", requirements={"id": "\d+"})
     */
    public function annulationSortie(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatManager $etatManager, $id)
    {
        $etatOuverte = $etatManager->recupererEtats('OUVERTE');
        $etatCloturee = $etatManager->recupererEtats('CLOTUREE');
        $etatAnnulee = $etatManager->recupererEtats('ANNULEE');
        $annulation = new AnnulationFormModel();
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();
        $annulationForm = $this->createForm(AnnulationFormType::class, $annulation);
        $annulationForm->handleRequest($request);

        if ($annulationForm->isSubmitted() && $annulationForm->isValid()) {
                if ($sortie->getOrganisateur() === $user and ($sortie->getEtat() === $etatOuverte or $sortie->getEtat() === $etatCloturee )) {
                    //On concatene le motif d'annulation dans la description de la sortie
                    $motif = $annulation->getMotif();
                    dump($motif);
                    $infos = $sortie->getInfosSortie();
                    dump($infos);
                    $infos = "".$infos ."  --------ATTENTION CETTE SORTIE EST ANNULEE !!--------  MOTIF : " .$motif;
                    $sortie->setInfosSortie($infos);

                    //On passe la sortie à l'état annulée
                    $sortie->setEtat($etatAnnulee);
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $this->addFlash('success', 'La sortie a bien été annulée !');
                } else {
                    $this->addFlash('error', 'Erreur : la sortie n\'a pas pu être annulée ! Veuillez contacter un administrateur');
                }

            return $this->redirectToRoute('sorties_liste');


        }

        return $this->renderForm('sortie/annulation.html.twig', [
            'annulation' => $annulation,
            'annulationForm' => $annulationForm,
        ]);


    }
}
