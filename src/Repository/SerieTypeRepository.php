<?php

namespace App\Repository;

use App\Entity\SerieType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SerieType>
 */
class SerieTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, SerieType::class);
    }


    /**
     * @param string|null $plexId
     * @param int|null    $tvdbId
     *
     * @return SerieType[] Returns an array of SerieType objects
     */
    public function findByPlexOrTVDB(?string $plexId = null, ?int $tvdbId = null): array
    {

        $qb = $this->createQueryBuilder('s');

        if ($plexId) {
            $qb->andWhere('plexId = :plexId')
                ->setParameter('plexId', $plexId);
        }

        if ($tvdbId) {
            $qb->andWhere('tvdbId = :tvdbId')
                ->setParameter('tvdbId', $tvdbId);
        }


        return $qb
            ->getQuery()
            ->getResult();

    }

    //    /**
    //     * @return SerieType[] Returns an array of SerieType objects
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

    //    public function findOneBySomeField($value): ?SerieType
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
