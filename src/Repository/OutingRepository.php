<?php

namespace App\Repository;

use App\Entity\Outing;
use App\Entity\Town;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outing::class);
    }

    public function findFullOuting($id)
    {
        // Fetch Places of the Town if there's a selected city
        $outingArrays = $this->createQueryBuilder("q")
            ->addSelect('place')
            ->addSelect('town')
            ->where("q.id = :id")
            ->setParameter("id", $id)
            ->join('q.place', 'place')
            ->join('place.town', 'town')
            ->getQuery()
            ->getResult();

        $outing = $outingArrays[0];

        return $outing;
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
