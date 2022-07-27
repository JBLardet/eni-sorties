<?php


namespace App\Service;


use App\Entity\Etat;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class EtatManager
{

    private EntityManager $entityManager;
    private EtatRepository $etatRepository;
    private SortieRepository $sortieRepository;


    public function __construct(EntityManagerInterface $entityManager, EtatRepository $etatRepository, SortieRepository $sortieRepository)
    {
        $this->entityManager = $entityManager;
        $this->etatRepository = $etatRepository;
        $this->sortieRepository = $sortieRepository;
    }

    public function recupererEtats(String $e): Etat
    {
        $monEtat = null;
        $etats = $this->etatRepository->findAll();

        foreach($etats as $etat) {

            if ($etat->getLibelle() == $e) {
                $monEtat = $etat;
            }
        }

        return $monEtat;
    }

    public function modificationAutomatiqueEtats() : bool
    {
        $sorties = $this->sortieRepository->findAllSaufEnCreationEtHistorisee();

        $etats = $this->etatRepository->findAll();

        foreach($etats as $etat) {

            if ($etat->getLibelle() == 'OUVERTE') {
                $etatOuverte = $etat;
            }
            if ($etat->getLibelle() == 'CLOTUREE') {
                $etatCloturee = $etat;
            }
            if ($etat->getLibelle() == 'EN COURS') {
                $etatEnCours = $etat;
            }
            if ($etat->getLibelle() == 'TERMINEE') {
                $etatTerminee = $etat;
            }
            if ($etat->getLibelle() == 'HISTORISEE') {
                $etatHistorisee = $etat;
            }
        }


            $now = new \DateTime();
            $now->modify('+ 2 hours');
            dump($now);


        foreach ($sorties as $sortie) {
            $dateHeureFinSortie = clone $sortie->getDateHeureDebut();
            $dateHeureFinSortie->modify('+ '.$sortie->getDuree().' minutes');

            $dateSortieAHistoriser = clone $sortie->getDateHeureDebut();
            $dateSortieAHistoriser->modify('+ 1 month');


            //passage etat -> historisée
            if($now > $dateSortieAHistoriser)
            {
                $sortie->setEtat($etatHistorisee);

            } elseif ($now > $dateHeureFinSortie and $sortie->getEtat()->getLibelle() !== 'ANNULEE')
            {   //passage etat -> terminee
                $sortie->setEtat($etatTerminee);
            } elseif ($sortie->getDateHeureDebut()< $now and $sortie->getEtat()->getLibelle() !== 'ANNULEE')
            {   //passage etat -> en cours
                $sortie->setEtat($etatEnCours);
            } elseif ($now > $sortie->getDateLimiteInscription() and $sortie->getEtat()->getLibelle() !== 'ANNULEE')

            {
                $sortie->setEtat($etatCloturee);
            }elseif ($now < $sortie->getDateLimiteInscription() and $sortie->getEtat()->getLibelle() !== 'ANNULEE')
            {
                $sortie->setEtat($etatOuverte);
            }

//            dump($sortie);
//            dump($now > $dateHeureFinSortie);
//            dump($sortie->getEtat()->getLibelle() !== 'ANNULEE');

            $this->entityManager->persist($sortie);

        }

        $this->entityManager->flush();

        return true;
    }


}