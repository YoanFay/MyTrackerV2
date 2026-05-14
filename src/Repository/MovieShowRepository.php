<?php

namespace App\Repository;

use App\Entity\MovieShow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MovieShow>
 */
class MovieShowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovieShow::class);
    }


    /**
     * @param string $startDate
     * @param string $endDate
     *
     * @return MovieShow[] Returns an array of EpisodeShow objects
     */
    public function getShowByDate(string $startDate, string $endDate): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.showDate >= :startDate')
            ->setParameter('startDate', $startDate)
            ->andWhere('m.showDate <= :endDate')
            ->setParameter('endDate', $endDate)
            ->orderBy('m.showDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return MovieShow[] Returns an array of MovieShow objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MovieShow
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
