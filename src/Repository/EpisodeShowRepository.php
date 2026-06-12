<?php

namespace App\Repository;

use App\Entity\EpisodeShow;
use App\Entity\Serie;
use App\Entity\User;
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
     * @param string $startDate
     * @param string $endDate
     *
     * @return EpisodeShow[] Returns an array of EpisodeShow objects
     */
    public function getShowByDate(string $startDate, string $endDate, User $user): array
    {

        return $this->createQueryBuilder('e')
            ->andWhere('e.showDate >= :startDate')
            ->andWhere('e.showDate <= :endDate')
            ->andWhere('e.user = :user')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('user', $user)
            ->orderBy('e.showDate', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param Serie $serie
     * @param User  $user
     *
     * @return EpisodeShow[] Returns an array of EpisodeShow objects
     */
    public function getShowBySerie(Serie $serie, User $user): array
    {

        return $this->createQueryBuilder('e')
            ->leftJoin('e.episode' , 'ep')
            ->andWhere('ep.serie = :serie')
            ->andWhere('e.user = :user')
            ->setParameter('serie', $serie)
            ->setParameter('user', $user)
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
