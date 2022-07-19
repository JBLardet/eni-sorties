<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->addEtats();
        $this->addVilles();
    }

    public function addEtats(){
        $listeEtats = ['En création', 'Ouverte', 'cloturée', 'En cours', 'terminée', 'historisée', 'annulée'];

        foreach ($listeEtats as $e) {
            $etat = new Etat();

            $etat->setLibelle($e);
            $this->manager->persist($etat);

        }

        $this->manager->flush();
    }

    public function addVilles(){


            $ville = new Ville();
            $ville->setNom('NIORT');
            $ville->setCodePostal(79000);
            $this->manager->persist($ville);




        $this->manager->flush();
    }
}


