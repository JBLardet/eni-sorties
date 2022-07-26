<?php


namespace App\Service;


use App\Entity\Etat;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;

class EtatManager
{

    private $entityManager;
    private $etatRepository;
    private $sortieRepository;


    public function __construct(EntityManagerInterface $entityManager, EtatRepository $etatRepository, SortieRepository $sortieRepository)
    {
        $this->entityManager = $entityManager;
        $this->etatRepository = $etatRepository;
        $this->sortieRepository = $sortieRepository;
    }

    public function recupererEtats(String $e): Etat
    {

        $etats = $this->etatRepository->findAll();

        foreach($etats as $etat) {

            if ($etat->getLibelle() == $e) {
                $monEtat = $etat;
            }
        }

        return $monEtat;
    }

    public function modificationAutomatiqueEtats() : void
    {
        $sorties = $this->sortieRepository->findAll();
        $etatEnCreation = $this->recupererEtats('EN CREATION');
        $etatOuverte = $this->recupererEtats('OUVERTE');
        $etatCloturee = $this->recupererEtats('CLOTUREE');
        $etatAnnulee = $this->recupererEtats('ANNULEE');
        $etatEnCours = $this->recupererEtats('EN COURS');
        $etatTerminee = $this->recupererEtats('TERMINEE');
        $etatHistorisee = $this->recupererEtats('HISTORISEE');



        foreach ($sorties as $sortie) {
            $dateHeureFinSortie = $sortie->getDateHeureDebut()->add(new DateInterval( '+ '.$sortie->getDuree().' minutes' ));
            $dateSortieAHistoriser = $dateHeureFinSortie->add(new DateInterval( '+ 1 month' ));

            //passage etat ouverte -> cloturee
            if($sortie->getEtat() === $etatOuverte and 'now' > $sortie->getDateLimiteInscription())
            {
                $sortie->setEtat($etatCloturee);
            }
            //passage etat cloturee -> en cours
            if(($sortie->getEtat() !== $etatAnnulee or $sortie->getEtat() !== $etatEnCreation) and $sortie->getDateHeureDebut()< 'now' and 'now' < $dateHeureFinSortie)
            {
                $sortie->setEtat($etatEnCours);
            }
            //passage etat en cours -> terminee
            if(($sortie->getEtat() !== $etatAnnulee or $sortie->getEtat() !== $etatEnCreation) and $dateHeureFinSortie< 'now' and 'now' < $dateSortieAHistoriser)
            {
                $sortie->setEtat($etatTerminee);
            }
            //passage etat terminee ou annulee -> historisée
            if($sortie->getEtat() !== $etatEnCreation and 'now' > $dateSortieAHistoriser)
            {
                $sortie->setEtat($etatHistorisee);
            }

        }

    }


}