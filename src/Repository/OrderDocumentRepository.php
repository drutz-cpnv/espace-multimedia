<?php

namespace App\Repository;

use App\Entity\OrderDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderDocument[]    findAll()
 * @method OrderDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDocument::class);
    }

    // /**
    //  * @return OrderDocument[] Returns an array of OrderDocument objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderDocument
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
