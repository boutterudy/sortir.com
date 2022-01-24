<?php

namespace App\Repository;

use App\Entity\Outlet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Outlet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outlet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outlet[]    findAll()
 * @method Outlet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outlet::class);
    }

    // /**
    //  * @return Outlet[] Returns an array of Outlet objects
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
    public function findOneBySomeField($value): ?Outlet
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
