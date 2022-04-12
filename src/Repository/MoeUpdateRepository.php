<?php

namespace App\Repository;

use App\Entity\MoeUpdate;
use App\Trait\Repository\StoreTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MoeUpdate|null find($id, $lockMode = null, $lockVersion = null)
 * @method MoeUpdate|null findOneBy(array $criteria, array $orderBy = null)
 * @method MoeUpdate[]    findAll()
 * @method MoeUpdate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoeUpdateRepository extends ServiceEntityRepository
{
    use StoreTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MoeUpdate::class);
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
