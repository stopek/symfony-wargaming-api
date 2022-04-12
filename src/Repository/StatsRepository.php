<?php

namespace App\Repository;

use App\Entity\Stats;
use App\Helpers\ArrayHelper;
use App\Trait\Repository\StoreTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stats[]    findAll()
 * @method Stats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatsRepository extends ServiceEntityRepository
{
    use StoreTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stats::class);
    }

    /**
     * @param array $players_ids
     * @return array
     */
    public function getPlayersBattles(array $players_ids): array
    {
        $stats = $this
            ->createQueryBuilder('s')
            ->select(['s.battles', 'player.id as player_id'])
            ->join('s.player', 'player')
            ->where('player.id IN(:ids)')
            ->setParameter('ids', $players_ids)
            ->getQuery()
            ->getResult();

        return ArrayHelper::arrayWithKeyFromMultidimensional($stats, 'player_id');
    }
}
