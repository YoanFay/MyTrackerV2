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
     * @param string $date
     *
     * @return MovieShow[] Returns an array of EpisodeShow objects
     */
    public function getShowByDate(string $date): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.showDate >= :date')
            ->setParameter('date', $date)
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
