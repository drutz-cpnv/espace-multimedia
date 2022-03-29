<?php

namespace App\Repository;

use App\Entity\UsedItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsedItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsedItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsedItem[]    findAll()
 * @method UsedItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsedItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsedItem::class);
    }

    // /**
    //  * @return UsedItem[] Returns an array of UsedItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsedItem
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
