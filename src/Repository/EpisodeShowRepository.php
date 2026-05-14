<?php

namespace App\Repository;

use App\Entity\EpisodeShow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EpisodeShow>
 */
class EpisodeShowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpisodeShow::class);
    }


    /**
     * @return EpisodeShow[] Returns an array of EpisodeShow objects
     */
    public function getShowByDate($date): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.showDate >= :date')
            ->setParameter('date', $date)
            ->orderBy('e.showDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return EpisodeShow[] Returns an array of EpisodeShow objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EpisodeShow
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
