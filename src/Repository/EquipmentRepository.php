<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Equipment;
use App\Entity\EquipmentSearch;
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

    public function findSearch(EquipmentSearch $search) {
        $query = $this->getSearchQuery($search)->getQuery();
        return $query->getResult();
    }

    private function getSearchQuery(EquipmentSearch $search) {
        $query = $this->createQueryBuilder('e')
            ->select('e', 'c', 'b', 't')
            ->join('e.categories', 'c')
            ->join('e.brand', 'b')
            ->join('e.type', 't');

        if(!$search->getCategories()->isEmpty()){
            $query = $query->andWhere('c.id in (:categories)')
                ->setParameter('categories', $search->getCategories());
        }

        if(!$search->getBrands()->isEmpty()){
            $query = $query->andWhere('b.id in (:brands)')
                ->setParameter('brands', $search->getBrands());
        }

        if(!$search->getTypes()->isEmpty()){
            $query = $query->andWhere('t.id in (:types)')
                ->setParameter('types', $search->getTypes());
        }

        return $query;
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
