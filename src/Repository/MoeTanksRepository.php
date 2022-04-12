<?php

namespace App\Repository;

use App\Entity\MoeTanks;
use App\Trait\Repository\StoreTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MoeTanks|null find($id, $lockMode = null, $lockVersion = null)
 * @method MoeTanks|null findOneBy(array $criteria, array $orderBy = null)
 * @method MoeTanks[]    findAll()
 * @method MoeTanks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoeTanksRepository extends ServiceEntityRepository
{
    use StoreTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MoeTanks::class);
    }

    public function getLastTankStatsWithTanks(int $update_id): array
    {
        return $this
            ->createQueryBuilder('m')
            ->join('m.update_owner', 'update_owner')
            ->andWhere('update_owner.id = :owner_id')->setParameter('owner_id', $update_id)
            ->orderBy('update_owner.version', 'desc')
            ->join('m.tank', 'tank')
            ->getQuery()
            ->getResult();
    }

}
