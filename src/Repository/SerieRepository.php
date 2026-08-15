<?php

namespace App\Repository;

use App\Entity\Serie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }


    /**
     * @param string|null $plexId
     * @param string|null $tvdbId
     *
     * @return Serie[]|null Returns an array of Music objects
     */
    public function findByPlexOrTvdbId(?string $plexId, ?string $tvdbId)
    {

        return $this->createQueryBuilder('s')
            ->where('s.plexId = :plexId')
            ->orWhere('s.tvdbId = :tvdbId')
            ->setParameter('plexId', $plexId)
            ->setParameter('tvdbId', $tvdbId)
            ->getQuery()
            ->getOneOrNullResult();

    }


    /**
     * @return Serie[]|null Returns an array of Music objects
     */
    public function getExpiredNextAired()
    {

        return $this->createQueryBuilder('s')
            ->andWhere('s.nextAired IS NOT NULL AND s.nextAired < :now')
            ->setParameter('now', date('Y-m-d'))
            ->andWhere("s.isFollow = true")
            ->getQuery()
            ->getResult();

    }


    /**
     * @return Serie[]|null Returns an array of Music objects
     */
    public function getFinished()
    {

        return $this->createQueryBuilder('s')
            ->andWhere('s.nextAired IS NULL')
            ->andWhere('s.status IN (:animeStatus,:tvdbStatus)')
            ->setParameter('animeStatus', "FINISHED")
            ->setParameter('tvdbStatus', "Ended")
            ->andWhere("s.isFollow = true")
            ->getQuery()
            ->getResult();

    }


    /**
     * @param User $user
     *
     * @return Serie[] Returns an array of Movie objects
     */
    public function seriesByUser(User $user): array
    {

        return $this->createQueryBuilder('s')
            ->select('s')
            ->leftJoin('s.episodes', 'e')
            ->leftJoin('e.episodeShows', 'es')
            ->andWhere('es.user = :user')
            ->setParameter('user', $user)
            ->groupBy('s.id')
            ->orderBy('MAX(es.showDate)', 'DESC')
            ->getQuery()
            ->getResult()
            ;

    }

    //    /**
    //     * @return Serie[] Returns an array of Serie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Serie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
