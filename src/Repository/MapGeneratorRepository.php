<?php

namespace App\Repository;

use App\Entity\MapGenerator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MapGenerator|null find($id, $lockMode = null, $lockVersion = null)
 * @method MapGenerator|null findOneBy(array $criteria, array $orderBy = null)
 * @method MapGenerator[]    findAll()
 * @method MapGenerator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapGeneratorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MapGenerator::class);
    }

    // /**
    //  * @return MapGenerator[] Returns an array of MapGenerator objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MapGenerator
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
