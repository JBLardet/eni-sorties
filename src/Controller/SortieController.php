<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\model\RechercheFormModel;
use App\Form\RechercheFormType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
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
    public function liste(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, Request $request): Response
    {
        $user = $this->getUser();
        $campus = $this->getUser()->getCampus();
        $rechercheModel = new RechercheFormModel();
        $rechercheModel->setCampus($campus);

        dump($rechercheModel);

        $form = $this->createForm(RechercheFormType::class, $rechercheModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($rechercheModel);
            return $this->render('sortie/index.html.twig', [
                'RechercheForm' => $form->createView(),
                'sorties' => $sortieRepository->findByFormulaire($rechercheModel, $user),
            ]);
        }
        return $this->render('sortie/index.html.twig', [
            'RechercheForm' => $form->createView(),
            'sorties' => $sortieRepository->findbyCampus($campus),
        ]);
    }



    /**
     * @Route("/NouvelleSortie", name="sortie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        dump($sortie);
        if ($form->isSubmitted() && $form->isValid()) {
            $etats = $etatRepository->findAll();
            foreach ($etats as $etat) {
                if ($etat->getLibelle() == 'EN CREATION') {
                    $etatENCREATION = $etat;
                }}
            $sortie->setEtat($etatENCREATION);
            $sortie->setOrganisateur($this->getUser());
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
     * @Route("/sortie/{id}/modifier", name="app_sortie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);

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
}
