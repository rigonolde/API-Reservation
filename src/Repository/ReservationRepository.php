<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function isTimeAlreadyTaken(\DateTime $startTime, \DateTime $endTime, int $carId): bool
    {
        return !!$this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('(r.startTime <= :startTime and r.endTime >= :startTime) or (r.startTime <= :endTime and r.endTime >= :endTime)')
            ->andWhere('r.car = :carId')
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->setParameter('carId', $carId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
