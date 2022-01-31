<?php

namespace App\Repository;

use App\Entity\Place;
use App\Entity\Town;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    /**
     * Return the Places linked to a Town or all Places if no Town was sent.
     * @param Town|null $town
     * @return Place[]|float|int|mixed|string
     */
    public function findByTown(Town $town = null)
    {
        if ($town) {
            // Fetch Places of the Town if there's a selected city
            $places = $this->createQueryBuilder("q")
                ->where("q.town = :townId")
                ->setParameter("townId", $town->getId())
                ->getQuery()
                ->getResult();
        } else { // No filter, unless there is a selected Town
            $places = $this->findAll();
        }

        return $places;
    }
    // /**
    //  * @return Place[] Returns an array of Place objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Place
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
