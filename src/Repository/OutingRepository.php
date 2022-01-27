<?php

namespace App\Repository;

use App\Entity\Outing;
use App\Entity\User;
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

    public function outingFilter (
        User $user,
        bool $organizer = false,
        string $start = null,
        string $stop =null,
        bool $passed = false)
    {

        $qb = $this->createQueryBuilder('s');


        if ($start != null){
            $starttime = strtotime($start);
            $starnewformat = date('Y-m-d', $starttime);
            $qb->andWhere('s.startAt >= :startAt')
                ->setParameter('startAt', $starnewformat);
        }
        if ($stop != null){
            $stoptime = strtotime($stop);
            $stopnewformat = date('Y-m-d', $stoptime);
            $qb->andWhere('s.limitSubscriptionDate <= :limitSubscriptionDate')
                ->setParameter('limitSubscriptionDate', $stopnewformat);

        }
        if ($passed){
            $qb ->andWhere('s.startAt <= :passed')
                ->setParameter('passed', date('Y-m-d HH:MM') );
        }
        if ($organizer){
            $qb ->andWhere('s.organizer = :organizer')
                ->setParameter('organizer', $user->getId());
        }

        $qb = $qb->getQuery();
        return $qb->execute();
    }

}
