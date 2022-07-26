<?php


namespace App\Service;


use App\Entity\Etat;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

class EtatManager
{

    private $entityManager;
    private $etatRepository;


    public function __construct(EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        $this->entityManager = $entityManager;
        $this->etatRepository = $etatRepository;
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


}