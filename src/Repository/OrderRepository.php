<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private UserRepository $userRepository, private OrderStateRepository $orderStateRepository, private StateRepository $stateRepository)
    {
        parent::__construct($registry, Order::class);
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
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

    /**
     * @return Order[] Returns an array of Order objects
     */
    public function findBetweenDates($dates)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.start BETWEEN :start AND :end')
            ->orWhere('o.end BETWEEN :start AND :end')
            ->setParameters(['start' => $dates[0], 'end' => $dates[1]])
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Order[] Returns an array of Order objects
     */
    public function findLate()
    {
        return $this->createQueryBuilder('o')
            ->select('o', 'os', 's')
            ->join('o.orderStates', 'os')
            ->join('os.state', 's')
            ->andWhere('o.end >= CURRENT_DATE()')
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', 'in_progress')
            ->orderBy('os.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function reset()
    {
        $tableName = $this->getClassMetadata()->getTableName();
        $connection = $this->getEntityManager()->getConnection();
        $connection->exec("ALTER TABLE `". $tableName ."` AUTO_INCREMENT = 1");
    }


    /**
     * @return Order[] Returns an array of Order objects
     */
    public function findByUser($user)
    {
        if($user instanceof UserInterface) {
            $user = $this->userRepository->find($user->getId());
        }

        return $this->createQueryBuilder('o')
            ->where('o.client = :user')
            ->setParameter('user', $user)
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Order
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
