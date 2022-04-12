<?php

namespace App\Factory;

use App\Collection\EntityCollection;
use App\Entity\Player;
use App\Entity\TanksStats;
use App\Repository\TankRepository;
use App\Repository\TanksStatsRepository;
use App\Wargaming\ApiResponse;

class TanksStatsFactory
{
    public function __construct(
        private TanksStatsRepository $tanksStatsRepository,
        private TankRepository       $tankRepository
    )
    {
    }

    /**
     * @param Player[] $ids_v_entity
     * @param ApiResponse[] $multiple_players_stats_response
     */
    public function createOrUpdate(array $ids_v_entity, array $multiple_players_stats_response)
    {
        $collection = new EntityCollection();
        $tanks = $this->tankRepository->getTanksWithIdKey();

        foreach ($multiple_players_stats_response as $api_response) {
            $stats = $api_response->getResponse()->first()->get();
            if (!isset($stats[0])) {
                continue;
            }

            $player = $ids_v_entity[$stats[0]['account_id']];
            $tanks_stats = $player->getTanksStats();

            foreach ($stats as $tank_stat) {
                $tank_id = $tank_stat['tank_id'];
                if (!isset($tanks[$tank_id])) {
                    continue;
                }

                $tank = $tanks[$tank_id];

                $player_tank_stat = $tanks_stats->filter(function (TanksStats $tank_stats) use ($tank) {
                    return $tank_stats->getTank()->getId() === $tank->getId();
                });


                $tankStatsItem = new TanksStats();
                if ($player_tank_stat->count() > 0) {
                    /** @var TanksStats $tankStatsItem */
                    $tankStatsItem = $player_tank_stat->first();

                    if (!$tankStatsItem->canUpdateStats($tank_stat['last_battle_time'])) {
                        continue;
                    }
                }

                $all = $tank_stat['all'];

                $tankStatsItem
                    ->setTank($tank)
                    ->setPlayer($player)
                    ->setSpotted($all['spotted'])
                    ->setPiercingsReceived($all['piercings_received'])
                    ->setHits($all['hits'])
                    ->setDamageAssistedTrack($all['damage_assisted_track'])
                    ->setWins($all['wins'])
                    ->setLosses($all['losses'])
                    ->setNoDamageDirectHitsReceived($all['no_damage_direct_hits_received'])
                    ->setCapturePoints($all['capture_points'])
                    ->setBattles($all['battles'])
                    ->setDamageDealt($all['damage_dealt'])
                    ->setExplosionHits($all['explosion_hits'])
                    ->setDamageReceived($all['damage_received'])
                    ->setPiercings($all['piercings'])
                    ->setShots($all['shots'])
                    ->setExplosionHitsReceived($all['explosion_hits_received'])
                    ->setDamageAssistedRadio($all['damage_assisted_radio'])
                    ->setXp($all['xp'])
                    ->setDirectHitsReceived($all['direct_hits_received'])
                    ->setFrags($all['frags'])
                    ->setSurvivedBattles($all['survived_battles'])
                    ->setDroppedCapturePoints($all['dropped_capture_points'])
                    ->setLastBattleTime($tank_stat['last_battle_time'])
                    ->setMaxXp($tank_stat['max_xp'])
                    ->setTreesCut($tank_stat['trees_cut'])
                    ->setMaxFrags($tank_stat['max_frags'])
                    ->setMarkOfMastery($tank_stat['mark_of_mastery'])
                    ->setBattleLifeTime($tank_stat['battle_life_time']);

                $collection->offsetSet('', $tankStatsItem);
            }
        }

        $this->tanksStatsRepository->saveMultiple($collection);
    }
}