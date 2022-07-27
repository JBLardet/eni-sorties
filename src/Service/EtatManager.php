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

        foreach ($sorties as $sortie) {
            $dateHeureFinSortie = clone $sortie->getDateHeureDebut();
            $dateHeureFinSortie->modify('+ '.$sortie->getDuree().' minutes');

            $dateSortieAHistoriser = clone $sortie->getDateHeureDebut();
            $dateSortieAHistoriser->modify('+ 1 month');

            //passage etat -> historisée
            if($now > $dateSortieAHistoriser)
            {
                $sortie->setEtat($etatHistorisee);
            }

            //passage etat -> terminee
            if($now > $dateHeureFinSortie and $sortie->getEtat()->getLibelle() !== 'ANNULEE')
            {
                $sortie->setEtat($etatTerminee);
            }

            //passage etat -> en cours
            if($sortie->getDateHeureDebut()< $now and $sortie->getEtat()->getLibelle() !== 'ANNULEE')
            {
                $sortie->setEtat($etatEnCours);
            }

            //passage etat -> cloturee
            if($now > $sortie->getDateLimiteInscription() and $sortie->getEtat()->getLibelle() !== 'ANNULEE')
            {
                $sortie->setEtat($etatCloturee);
            }


        }
        return true;
    }


}