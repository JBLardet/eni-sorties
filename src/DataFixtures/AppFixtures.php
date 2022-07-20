<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;
    private UserPasswordHasherInterface $hasher;

    public function load(ObjectManager $manager): void
    {
        $this->manager=$manager;
        $this->addUser();
    }

    public function __construct(userPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function addUser(){

         for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user
                ->setPseudo('pseudo '.$i)
                ->setName('Nom'.$i)
                ->setFirstName('Prénom'.$i)
                ->setPhone('01 23 45 67 89')
                ->setEmail("Nom$i@mail.com");
            $psw = $this->hasher->hashPassword($user,'123456');
            $user
                ->setPassword($psw)
                ->setActive('1')
                ->setRoles(['ROLE_USER']);

            $this->manager->persist($user);
            }

        $this->manager->flush();

        }
}
