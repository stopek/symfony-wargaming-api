<?php

namespace App\Repository;

use App\Entity\ExpUpdate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExpUpdate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpUpdate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpUpdate[]    findAll()
 * @method ExpUpdate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpUpdateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpUpdate::class);
    }

    public function getLastUpdateVersion(): ?array
    {
        return $this
            ->createQueryBuilder('u')
            ->select('u.version, u.id')
            ->orderBy('u.version', 'desc')
            ->addOrderBy('u.id', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
