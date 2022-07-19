<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $ListeEtats = ['En création', 'Ouverte', 'cloturée', 'En cours', 'terminée', 'historisée', 'annulée'];

        for ($listeEtat as $etat) {
        $etat = new Etat();

        $etat->setLibelle("En création");
        $manager->persist($etat);

        $etat->setLibelle("Ouverte");
        $manager->persist($etat);

        $etat->setLibelle("Cloturée");
        $manager->persist($etat);

        $etat->setLibelle("Activité en cours");
        $manager->persist($etat);

        $etat->setLibelle("Activité terminée");
        $manager->persist($etat);

        $etat->setLibelle("Terminée");
        $manager->persist($etat);

        $etat->setLibelle("Historisée");
        $manager->persist($etat);

        $etat->setLibelle("Ouverte");
        $manager->persist($etat);

    }

        $manager->flush();
    }
}
