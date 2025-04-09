<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findFiltered(
        ?string $location = null,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null,
        int $page = 1,
        int $limit = 10
    ): array {
        $qb = $this->createQueryBuilder('e');

        if ($location) {
            $qb->andWhere('e.location = :location')
               ->setParameter('location', $location);
        }

        if ($from) {
            $qb->andWhere('e.startTime >= :from')
               ->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere('e.endTime <= :to')
               ->setParameter('to', $to);
        }

        return $qb
            ->orderBy('e.startTime', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countFiltered(
        ?string $location = null,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null
    ): int {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)');

        if ($location) {
            $qb->andWhere('e.location = :location')
               ->setParameter('location', $location);
        }

        if ($from) {
            $qb->andWhere('e.startTime >= :from')
               ->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere('e.endTime <= :to')
               ->setParameter('to', $to);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
