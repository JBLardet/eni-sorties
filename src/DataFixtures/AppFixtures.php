<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use App\Service\EtatManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;


    private VilleRepository $villeRepo;
    private LieuRepository $lieuRepository;
    private EtatRepository $etatRepository;
    private CampusRepository $campusRepository;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $hasher;
    private Generator $generator;
    private EtatManager $etatManager;

    public function __construct(

                                    VilleRepository $villeRepo,
                                    LieuRepository $lieuRepository,
                                    EtatRepository $etatRepository,
                                    EtatManager $etatManager,
                                    CampusRepository $campusRepository,
                                    UserRepository $userRepository,
                                    userPasswordHasherInterface $hasher)

    {
        $this->villeRepo = $villeRepo;
        $this->lieuRepository = $lieuRepository;
        $this->etatRepository = $etatRepository;
        $this->campusRepository = $campusRepository;
        $this->userRepository = $userRepository;
        $this->generator = Factory::create("fr_FR");
        $this->hasher = $hasher;
        $this->etatManager = $etatManager;
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->addEtats();
        $this->addVilles();
        $this->addLieux();
        $this->addCampus();
        $this->addUser();
        $this->addSorties();

    }

    public function addEtats(){
        $listeEtats = ['EN CREATION', 'OUVERTE', 'CLOTUREE', 'EN COURS', 'TERMINEE', 'HISTORISEE', 'ANNULEE'];

        foreach ($listeEtats as $e) {
            $etat = new Etat();

            $etat->setLibelle($e);
            $this->manager->persist($etat);

        }

        $this->manager->flush();
    }

    public function addVilles(){



            $ville = new Ville();
            $ville->setNom('NANTES');
            $ville->setCodePostal(44000);
            $this->manager->persist($ville);

            $ville = new Ville();
            $ville->setNom('NIORT');
            $ville->setCodePostal(79000);
            $this->manager->persist($ville);


        $ville = new Ville();
        $ville->setNom('QUIMPER');
        $ville->setCodePostal(29000);
        $this->manager->persist($ville);

        $ville = new Ville();
        $ville->setNom('RENNES');
        $ville->setCodePostal(35000);
        $this->manager->persist($ville);


        $this->manager->flush();

    }

    public function addLieux(){

        $villes = $this->villeRepo->findAll();

        foreach ($villes as $ville) {

            $lieu = new Lieu();
            $lieu->setNom('Cinéma');
            $lieu->setRue('Rue des Cinémas');
            $lieu->setLatitude(45.9656637);
            $lieu->setLongitude(6.9887496);
            $lieu->setVille($ville);
            $this->manager->persist($lieu);

            $lieu = new Lieu();
            $lieu->setNom('Bar');
            $lieu->setRue('Rue des Poivrots');
            $lieu->setLatitude(43.9426637);
            $lieu->setLongitude(4.9887496);
            $lieu->setVille($ville);
            $this->manager->persist($lieu);

            $lieu = new Lieu();
            $lieu->setNom('Musée');
            $lieu->setRue('Rue des artistes');
            $lieu->setLatitude(42.9438837);
            $lieu->setLongitude(5.9857496);
            $lieu->setVille($ville);
            $this->manager->persist($lieu);

            $lieu = new Lieu();
            $lieu->setNom('Piscine Municipale');
            $lieu->setRue('Rue des sportifs');
            $lieu->setLatitude(51.5736637);
            $lieu->setLongitude(3.9856496);
            $lieu->setVille($ville);
            $this->manager->persist($lieu);

        }

        $this->manager->flush();
    }

    private function addCampus()
    {

        $listeCampus = ['SAINT HERBLAIN', 'CHARTRES DE BRETAGNE', 'LA ROCHE SUR YON'];

        foreach ($listeCampus as $c) {
            $campus = new Campus();

            $campus->setNom($c);
            $this->manager->persist($campus);

        }

        $this->manager->flush();
    }

    public function addUser(){

        $campus = $this->campusRepository->findAll();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user

                ->setPseudo('pseudo'.$i)
                ->setNom($this->generator->lastName)
                ->setPrenom($this->generator->firstName)

                ->setTel($this->generator->phoneNumber)

                ->setEmail("Nom$i@mail.com");
            $psw = $this->hasher->hashPassword($user,'123456');
            $user
                ->setPassword($psw)
                ->setActif('1')
                ->setRoles(["ROLE_USER"])
                ->setCampus($this->generator->randomElement($campus));

            $this->manager->persist($user);
        }

        $this->manager->flush();

    }

    public function addSorties(){

        $campus = $this->campusRepository->findAll();
        $lieux = $this->lieuRepository->findAll();

        $users = $this->userRepository->findAll();

        $etats = $this->etatRepository->findAll();

        $etatOuverte = $this->etatManager->recupererEtats('OUVERTE');
        $etatEnCreation = $this->etatManager->recupererEtats('EN CREATION');


        for ($i=0; $i<25; $i++) {

            $sortie = new Sortie();
            $sortie->setNom('sortie' . $i);
            $sortie->setDateHeureDebut($this->generator->dateTimeBetween('- 1 week', '+ 1 week'));

            $dateSortie = clone $sortie->getDateHeureDebut();
            $dateCloture = $dateSortie->modify('-2 days');

            $sortie->setDuree(120);
            $sortie->setDateLimiteInscription($dateCloture);
            $sortie->setInfosSortie("La sortie de l'année!");
            $sortie->setNbInscriptionsMax($this->generator->numberBetween(6, 12));
            $sortie->setEtat($this->generator->randomElement($etats));
            $sortie->setLieu($this->generator->randomElement($lieux));
            $sortie->setOrganisateur($this->generator->randomElement($users));
            $sortie->setCampus($this->generator->randomElement($campus));

            if ($sortie->getEtat() !== $etatEnCreation) {
            for($j=0; $j<($this->generator->numberBetween(0, 10)); $j++) {
                $sortie->addParticipant($this->generator->randomElement($users));
            }
            }
            $this->manager->persist($sortie);

        }
        $this->manager->flush();
    }


}