<?php

namespace App\Repository;

use App\Entity\Outing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $connection = $entityManager->getConnection();
        $stmt = $connection->prepare('CALL updateOutingsStatus();');
        $stmt->executeQuery();
        parent::__construct($registry, Outing::class);
    }

    public function findFullOuting($id)
    {
        // Fetch Places of the Town if there's a selected city
        $outing = $this->createQueryBuilder("q")
            ->where("q.id = :id")
            ->setParameter("id", $id)
            ->getQuery()
            ->getResult();

        return $outing;
    }

    public function outingFilter (
        User $user,
        bool $organizer = false,
        string $start = null,
        string $stop =null,
        bool $passed = false)
    {

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