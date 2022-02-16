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
    private ManagerRegistry $registry;
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->registry = $registry;
        parent::__construct($this->registry, Outing::class);
    }

    public function updateStatuses(){
        $connection = $this->entityManager->getConnection();
        $stmt = $connection->prepare('CALL updateOutingsStatus();');
        $stmt->executeQuery();
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

    public function outingFilter($user, $name, $organizer, $campus, $start, $stop, $subscribed, $unsubscribed, $passed)
    {
        $qb = $this->createQueryBuilder('s');


        if ($campus) {
            $qb->andWhere('s.campus = :campus')
                ->setParameter('campus', $campus);
        }
        if ($name) {
            $qb->andWhere('s.name = :name')
                ->setParameter('name', $name);
        }
        if ($start) {
            $qb->andWhere('s.startAt >= :startAt')
                ->setParameter('startAt', $start);
        }
        if ($stop) {
            $qb->andWhere('s.limitSubscriptionDate <= :limitSubscriptionDate')
                ->setParameter('limitSubscriptionDate', $stop);
        }
        if ($organizer) {
            $qb ->andWhere('s.organizer = :organizer')
                ->setParameter('organizer', $user->getId());
        }
        if ($subscribed) {
            $qb->andWhere('s.users MEMBER OF :user')
                ->setParameter('subscribed', $user->getId());
        }
        if ($unsubscribed) {
            $qb->andWhere('s.users NOT MEMBER OF :user')
                ->setParameter('unsubscribed', $user->getId());
        }
        if ($passed) {
            $qb ->andWhere('s.startAt <= :passed')
                ->setParameter('passed', date('Y-m-d HH:MM'));
        }


        return $qb->getQuery()->getResult();
    }
}
