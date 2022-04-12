<?php

namespace App\Repository;

use App\Entity\Player;
use App\Entity\TanksStats;
use App\Trait\Repository\StoreTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TanksStats|null find($id, $lockMode = null, $lockVersion = null)
 * @method TanksStats|null findOneBy(array $criteria, array $orderBy = null)
 * @method TanksStats[]    findAll()
 * @method TanksStats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TanksStatsRepository extends ServiceEntityRepository
{
    use StoreTrait;

    private array $columns = [
        'ts.spotted', 'ts.damage_assisted_track', 'ts.damage_dealt',
        'ts.frags', 'ts.capture_points', 'ts.wins', 'ts.battles', 'ts.dropped_capture_points',
        'player.id as player_id', 'tank.tag as tank_tag', 'tank.id as tank_id', 'tank.tier as tank_tier'
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TanksStats::class);
    }

    /**
     * @param Player $player
     * @return float|null
     */
    public function getAverageTanksTier(Player $player): ?float
    {
        $data = $this
            ->createQueryBuilder('ts')
            ->select('AVG(tank.tier) as avg_tier')
            ->join('ts.tank', 'tank')
            ->where('ts.player = :player')->setParameter('player', $player)
            ->andWhere('tank.tier >= :min_tier')->setParameter('min_tier', 1)
            ->getQuery()
            ->getOneOrNullResult();

        return $data['avg_tier'];
    }

    public function getSummaryTanksData(Player $player): array
    {
        return $this
            ->createQueryBuilder('ts')
            ->select('
                SUM(ts.battles) as battles, 
                SUM(ts.damage_dealt) as damage_dealt, 
                SUM(ts.frags) as frags,
                SUM(ts.spotted) as spotted,
                SUM(ts.wins) as wins,
                SUM(ts.capture_points) as capture_points,
                SUM(ts.dropped_capture_points) as dropped_capture_points
            ')
            ->join('ts.tank', 'tank')
            ->where('ts.player = :player')->setParameter('player', $player)
            ->andWhere('tank.tier >= :min_tier')->setParameter('min_tier', 1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getTanksStatsForPlayers(array|int $players_ids, int $tank_id = 0): array
    {
        $query = $this
            ->createQueryBuilder('ts')
            ->select($this->columns)
            ->where('ts.player IN(:ids)')
            ->join('ts.player', 'player')
            ->join('ts.tank', 'tank')
            ->andWhere('tank.tier >= :min_tier')->setParameter('min_tier', 1)
            ->setParameter('ids', $players_ids);

        if ($tank_id > 0) {
            $query->andWhere('tank.id = :tank_id')->setParameter('tank_id', $tank_id);
        }

        $output = [];

        if (!is_array($players_ids)) {
            if ($tank_id > 0) {
                return $query->getQuery()->getOneOrNullResult();
            }

            $data = $query
                ->getQuery()
                ->getResult();

            foreach ($data as $item) {
                $output[$item['tank_tag']] = $item;
            }

            return $output;
        }

        $data = $query
            ->getQuery()
            ->getResult();

        foreach ($data as $item) {
            $output[$item['player_id']][$item['tank_tag']] = $item;
        }

        return $output;
    }

    /**
     * @param int $tank_id
     * @return []
     */
    public function getTanksStatsForTankId(int $tank_id): array
    {
        return $this
            ->createQueryBuilder('ts')
            ->select($this->columns)
            ->where('tank.id = :tank_id')
            ->setParameter('tank_id', $tank_id)
            ->join('ts.tank', 'tank')
            ->join('ts.player', 'player')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Player $player
     * @return TanksStats[]
     */
    public function getRecentlyPlayedTanks(Player $player, int $limit = 5): array
    {
        $data = $this
            ->createQueryBuilder('ts')
            ->select('ts')
            ->addSelect('tank')
            ->addSelect('player')
            ->where('ts.player = :player')->setParameter('player', $player)
            ->join('ts.tank', 'tank')
            ->join('ts.player', 'player')
            ->setMaxResults($limit)
            ->orderBy('CAST(ts.last_battle_time AS SIGNED)', 'DESC');

        $data = $data
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        $output = [];
        foreach ($data as $item) {
            $output[$item['tank']['tag']] = array_merge($item, [
                'tank_id' => $item['tank']['id'],
                'tank_tag' => $item['tank']['tag'],
                'player_id' => $item['player']['id']
            ]);
        }

        return $output;
    }
}
