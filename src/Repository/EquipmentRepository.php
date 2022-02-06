<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Equipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Equipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipment[]    findAll()
 * @method Equipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
    }

     /**
      * @return Equipment[] Returns an array of Equipment objects
    */
    public function findAllEnabled()
    {
        return $this->findVisibleQuery()
            ->getQuery()
            ->getResult()
        ;
    }


    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.enabled = :val')
            ->setParameter('val', true);
    }

    /*
    public function findOneBySomeField($value): ?Equipment
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
