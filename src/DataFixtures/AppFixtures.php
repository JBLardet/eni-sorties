<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->addEtat();
    }

    public function addEtat(){
        $listeEtats = ['En création', 'Ouverte', 'cloturée', 'En cours', 'terminée', 'historisée', 'annulée'];

        foreach ($listeEtats as $e) {
            $etat = new Etat();

            $etat->setLibelle($e);
            $this->manager->persist($etat);

        }

        $this->manager->flush();
    }
}


