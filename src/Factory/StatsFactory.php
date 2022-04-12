<?php

namespace App\Factory;

use App\Collection\EntityCollection;
use App\Entity\Player;
use App\Entity\Stats;
use App\Entity\Tank;
use App\Repository\StatsRepository;
use App\Repository\TankRepository;

class StatsFactory
{
    public function __construct(
        private StatsRepository $statsRepository,
        private TankRepository  $tankRepository
    )
    {
    }

    public function createOrUpdate(array $ids_v_entity, array $response_player_details)
    {
        $collection = new EntityCollection();

        foreach ($response_player_details as $account_id => $details) {
            /** @var Player $player */
            $player = $ids_v_entity[$account_id] ?? null;
            if (null === $player) {
                continue;
            }

            $statistic = $details['statistics'];
            $all = $statistic['all'];

            if ($player->getStats()->count() > 0) {
                $statisticItem = $player->getStats()->first();
            } else {
                $statisticItem = new Stats();
            }

            $maxXpTank = $this->getTankIfNotEmpty($statistic['max_xp_tank_id']);
            $maxFragsTank = $this->getTankIfNotEmpty($statistic['max_frags_tank_id']);
            $maxDamageTank = $this->getTankIfNotEmpty($statistic['max_damage_tank_id']);

            $statisticItem
                ->setPlayer($player)
                ->setMaxXpTank($maxXpTank)
                ->setMaxFragsTank($maxFragsTank)
                ->setMaxDamageTank($maxDamageTank)
                ->setMaxXp($statistic['max_xp'])
                ->setMaxFrags($statistic['max_frags'])
                ->setMaxDamage($statistic['max_damage'])
                ->setExplosionHits($statistic['explosion_hits'])
                ->setDamageAssistedTrack($statistic['damage_assisted_track'])
                ->setPiercings($statistic['piercings'])
                ->setTreesCut($statistic['trees_cut'])
                ->setPiercingsReceived($statistic['piercings_received'])
                ->setNoDamageDirectHitsReceived($statistic['no_damage_direct_hits_received'])
                ->setExplosionHitsReceived($statistic['explosion_hits_received'])
                ->setFrags($all['frags'])
                ->setDirectHitsReceived($statistic['direct_hits_received'])
                ->setDamageAssistedRadio($statistic['damage_assisted_radio'])
                ->setSpotted($all['spotted'])
                ->setHits($all['hits'])
                ->setWins($all['wins'])
                ->setLosses($all['losses'])
                ->setCapturePoints($all['capture_points'])
                ->setBattles($all['battles'])
                ->setDamageDealt($all['damage_dealt'])
                ->setDamageReceived($all['damage_received'])
                ->setShots($all['shots'])
                ->setXp($all['xp'])
                ->setSurvivedBattles($all['survived_battles'])
                ->setDroppedCapturePoints($all['dropped_capture_points']);

            $collection->offsetSet('', $statisticItem);
        }

        $this->statsRepository->saveMultiple($collection);
    }

    private function getTankIfNotEmpty($tank_id): ?Tank
    {
        $tanks_id = (int)$tank_id;
        if ($tank_id <= 0) {
            return null;
        }

        return $this->tankRepository->find($tank_id);
    }
}