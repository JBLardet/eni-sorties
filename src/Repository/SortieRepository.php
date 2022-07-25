<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\model\RechercheFormModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @param RechercheFormModel $rechercheFormModel
     * @return Sortie[]
     */
    public function findByFormulaire(RechercheFormModel $rechercheFormModel, User $user)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->join('s.campus', 'c');
        $queryBuilder->join('s.etat', 'e');
        $queryBuilder->join('s.organisateur', 'o');
        $queryBuilder->leftJoin('s.participants', 'p')
            ->addSelect('c', 'e', 'o', 'p');

        if($rechercheFormModel->getCampus()) {
            $queryBuilder->andWhere('s.campus = :campus');
            $queryBuilder->setParameter('campus', $rechercheFormModel->getCampus());
        }
        if($rechercheFormModel->getRechercheParNom()) {
            $queryBuilder->andWhere('s.nom LIKE :value');
            $queryBuilder->setParameter('value', '%'.$rechercheFormModel->getRechercheParNom().'%');

        }
        if ($rechercheFormModel->getDateMin() != null) {
            $queryBuilder->andWhere('s.dateHeureDebut > :dateMin');
            $queryBuilder->setParameter('dateMin', $rechercheFormModel->getDateMin());

        }
        if ($rechercheFormModel->getDateMax()!= null) {
            $dateMax = $rechercheFormModel->getDateMax();
            $dateMax = $dateMax->modify('+ 1 day');
            $queryBuilder->andWhere('s.dateHeureDebut < :dateMax');
            $queryBuilder->setParameter('dateMax', $dateMax);
        }
       if($rechercheFormModel->getOrganisateur() != null) {
            $queryBuilder->andWhere('s.organisateur = :user');
            $queryBuilder->setParameter('user', $user);
        }

        if($rechercheFormModel->getParticipant() != null) {
            $queryBuilder->andWhere(':user MEMBER OF s.participants');
            $queryBuilder->setParameter('user', $user);
        }

        if($rechercheFormModel->getNonParticipant() != null) {
            $queryBuilder->andWhere(':user NOT MEMBER OF s.participants');
            $queryBuilder->setParameter('user', $user);
        }

        if($rechercheFormModel->getSortiesPassees() != null) {
            $queryBuilder->andWhere('e.libelle = :value');
            $queryBuilder->setParameter('value', 'TERMINEE');
        }
        $queryBuilder->orderBy('s.dateHeureDebut', 'ASC');

        $queryBuilder->andWhere('e.libelle != :val and e.libelle != :encrea');
        $queryBuilder->setParameter('val', 'HISTORISEE');
        $queryBuilder->setParameter('encrea', 'EN CREATION');

        $queryBuilder->orWhere('e.libelle = :encreation and s.organisateur = :user');
        $queryBuilder->setParameter('encreation', 'EN CREATION');
        $queryBuilder->setParameter('user', $user);

        $queryBuilder->orderBy('s.nom');


        $query = $queryBuilder->getQuery();
        $result = $query->getResult();

        return $result;

    }


}
