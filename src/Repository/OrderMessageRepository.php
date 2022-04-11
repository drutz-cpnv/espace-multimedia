<?php

namespace App\Repository;

use App\Entity\OrderMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderMessage[]    findAll()
 * @method OrderMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderMessage::class);
    }

    public function findOneByToken(string $token): ?OrderMessage
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.token = :val')
            ->setParameter('val', $token)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return OrderMessage[] Returns an array of OrderMessage objects
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
    public function findOneBySomeField($value): ?OrderMessage
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
