<?php

namespace App\Helpers;

use App\Collection\AvgCollection;
use App\Entity\Clan;
use App\Entity\Player;
use App\Entity\Stats;
use App\Entity\Tank;
use App\Repository\ExpTanksRepository;
use App\Repository\PlayerRepository;
use App\Repository\StatsRepository;
use App\Repository\TanksStatsRepository;
use App\Wargaming\WNInfo;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Calculations
{
    public function __construct(
        private PlayerRepository     $playerRepository,
        private TanksStatsRepository $tanksStatsRepository,
        private ExpTanksRepository   $expTanksRepository,
        private StatsRepository      $statsRepository
    )
    {
    }

    #[Pure]
    public function clanWN(array $clan_players_wn): float|int
    {
        return $this->division($clan_players_wn['sum'], $clan_players_wn['total']);
    }

    #[Pure]
    public function division($a, $b, $precision = 5): float|int
    {
        $a = floatval($a);
        $b = floatval($b);

        if ($a === 0.00 || $b === 0.00) {
            return 0;
        }

        return $this->round($a / $b, $precision);
    }

    private function round(float|int $value, $precision = 5): float|int
    {
        return round($value, $precision);
    }

    /**
     * @param Player[] $players
     * @return array
     */
    public function playersWN8(iterable $players): array
    {
        $players = ArrayHelper::arrayWithKeyFromObjectCollection($players, function (Player $player) {
            return $player->getId();
        });

        $tanksExpected = $this->expTanksRepository->getLastTankStats();
        $playersTanksStats = $this->tanksStatsRepository->getTanksStatsForPlayers(array_keys($players));

        $output = [];

        foreach ($playersTanksStats as $playerId => $playerTanksStats) {
            /** @var Stats $stats */
            $stats = $players[$playerId]->getStats()->first();

            $output[$playerId] = $this->calculateWN8ForPlayer($playerTanksStats, $tanksExpected, $stats->getBattles());
        }

        return $output;
    }

    public function calculateWN8ForPlayer(array $playerTanksStats, array $tanksExpected, int $battles): float|int
    {
        $sum = 0;
        $weight = 0;
        foreach ($playerTanksStats as $tankTag => $tankStat) {
            $tankExpected = $tanksExpected[$tankTag] ?? null;
            if (null === $tankExpected) {
                continue;
            }

            $result = $this->calculateTankWN8($tankStat, $tankExpected, $battles);
            if ($result->isValid()) {
                $sum += $result->getWnSum();
                $weight += $result->getWeight();
            }
        }

        if (empty($sum) || empty($weight)) {
            return -1;
        }

        return $this->division($sum, $weight);
    }

    /**
     * @param array $tankStat - statystyki gracza na czołgu
     * @param array $tankExpected - wartości expected na danym czołgu
     * @param int $battles - sumaryczna ilość bitew danego gracza
     * @return WNInfo
     */
    public function calculateTankWN8(array $tankStat, array $tankExpected, int $battles): WNInfo
    {
        $result = new WNInfo(0, 0);
        $tank_battles = $tankStat['battles'];

        $avgDamage = $this->division($tankStat['damage_assisted_track'] + $tankStat['damage_dealt'], $tank_battles);
        $rDamage = $this->division($avgDamage, $tankExpected['damage']);

        $avgSpot = $this->division($tankStat['spotted'], $tank_battles);
        $rSpot = $this->division($avgSpot, $tankExpected['spot']);

        $avgFrag = $this->division($tankStat['frags'], $tank_battles);
        $rFrag = $this->division($avgFrag, $tankExpected['frag']);

        $avgDef = $this->division($tankStat['capture_points'], $tank_battles);
        $rDef = $this->division($avgDef, $tankExpected['def']);

        $avgWin = $this->division($tankStat['wins'], $tank_battles);
        $rWin = $this->division($avgWin, $tankExpected['win']);

        $rWinC = max(0, ($rWin - 0.71) / (1 - 0.71));
        $rDamageC = max(0, ($rDamage - 0.22) / (1 - 0.22));
        $rFragC = max(0, min($rDamageC + 0.2, ($rFrag - 0.12) / (1 - 0.12)));
        $rSpotC = max(0, min($rDamageC + 0.1, ($rSpot - 0.38) / (1 - 0.38)));
        $rDefC = max(0, min($rDamageC + 0.1, ($rDef - 0.10) / (1 - 0.10)));

        $weight = (100 * $tank_battles) / $battles;
        $wn8 = $this->round(
            $this->round(980 * $rDamageC) +
            $this->round(210 * $rDamageC * $rFragC) +
            $this->round(155 * $rFragC * $rSpotC) +
            $this->round(75 * $rDefC * $rFragC) +
            $this->round(145 * min(1.8, $rWinC))
        );

        $result->setWeight($weight);
        $result->setWN($wn8);

        return $result;
    }

    public function calculateWeightAvg(array $list, string $param_key): float|int
    {
        $totalWeight = ArrayHelper::multidimensionalSum($list, 'weight');
        $sum = 0;

        foreach ($list as $item) {
            $sum += $item[$param_key] * $item['weight'];
        }

        return $this->division($sum, $totalWeight);
    }

    #[ArrayShape(['players' => "array", 'total' => "int", 'sum' => "float|int"])]
    public function clanPlayersWN7(Clan $clan): array
    {
        $clanPlayers = $this->playerRepository->getPlayerIdsFromClanCollection($clan);
        $result = $this->playersWN7($clanPlayers);

        $sum = 0;
        $total = 0;

        foreach ($result as $wn) {
            if ($wn > 0) {
                $sum += floatval($wn);
                $total++;
            }
        }

        return [
            'players' => $result,
            'total' => $total,
            'sum' => $sum
        ];
    }

    /**
     * @param Player[] $players
     * @return array
     */
    public function playersWN7(iterable $players): array
    {
        /** @var Player[] $players */
        $players = ArrayHelper::arrayWithKeyFromObjectCollection($players, function (Player $player) {
            return $player->getId();
        });

        $output = [];
        foreach ($players as $playerId => $player) {
            $output[$playerId] = $this->playerWN7($player);
        }

        return $output;
    }

    public function playerWN7(Player $player): float|int
    {
        $playersTanksStats = $this->getPlayerTanksStats($player);
        $battles = ArrayHelper::multidimensionalSum($playersTanksStats, 'battles');

        $sum = 0;
        $weight = 0;
        $battles2 = 0;

        foreach ($playersTanksStats as $tanksStat) {
            $result = $this->calculateTankWN7([
                'battles' => $tanksStat['battles'],
                'damage_dealt' => $tanksStat['damage_dealt'],
                'frags' => $tanksStat['frags'],
                'spotted' => $tanksStat['spotted'],
                'capture_points' => $tanksStat['capture_points'],
                'wins' => $tanksStat['wins']
            ], $tanksStat['tank_tier'], $battles);
            $battles2 += $tanksStat['battles'];

            if ($result->isValid()) {
                $sum += $result->getWnSum();
                $weight += $result->getWeight();
            }
        }

        if (empty($sum) || empty($weight)) {
            return -1;
        }

        return $this->division($sum, $weight);
    }

    private function getPlayerTanksStats(Player $player): array
    {
        return $this->tanksStatsRepository->getTanksStatsForPlayers($player->getId());
    }

    public function calculateTankWN7(array $tankStats, int $tier, int $battles): WNInfo
    {
        $result = new WNInfo(0, 0);

        $tank_battles = $tankStats['battles'];

        $frags = $this->division($tankStats['frags'], $tank_battles);
        $damage = $this->division($tankStats['damage_dealt'], $tank_battles);
        $spotted = $this->division($tankStats['spotted'], $tank_battles);
        $def = $this->division($tankStats['capture_points'], $tank_battles);

        $win_rate = $this->division(100 * $tankStats['wins'], $tank_battles);

        $weight = round((100 * $tank_battles) / $battles, 11);
        $wn7 = $this->round(
            (1240 - (1040 / pow(MIN($tier, 6), 0.164))) * $frags
            + $damage * (530 / (184 * exp(0.24 * $tier) + 130))
            + $spotted * 125 * (MIN($tier, 3) / 3)
            + MIN($def, 2.2) * 100
            + ((185 / (0.17 + exp(($win_rate - 35) * -0.134))) - 500) * 0.45
            - (
                ((5 - MIN($tier, 5)) * 125) /
                (1 + exp((($tier - pow($battles / 220, (3 / $tier))) * 1.5)))
            )
        );

        $result->setWN($wn7);
        $result->setWeight($weight);

        return $result;
    }

    #[ArrayShape(['players' => "array", 'total' => "int", 'sum' => "float|int"])]
    public function clanPlayersEfficiency(Clan $clan): array
    {
        $clanPlayers = $this->playerRepository->getPlayerIdsFromClanCollection($clan);
        $result = $this->playersEfficiency($clanPlayers);

        $sum = 0;
        $total = 0;

        foreach ($result as $wn) {
            if ($wn > 0) {
                $sum += floatval($wn);
                $total++;
            }
        }

        return [
            'players' => $result,
            'total' => $total,
            'sum' => $sum
        ];
    }

    /**
     * @param Player[] $players
     * @return array
     */
    public function playersEfficiency(iterable $players): array
    {
        /** @var Player[] $players */
        $players = ArrayHelper::arrayWithKeyFromObjectCollection($players, function (Player $player) {
            return $player->getId();
        });

        $output = [];
        foreach ($players as $playerId => $player) {
            $output[$playerId] = $this->playerEfficiency($player);
        }

        return $output;
    }

    public function playerEfficiency(Player $player): float|int
    {
        $avgTier = $this->tanksStatsRepository->getAverageTanksTier($player);

        if (!$avgTier) {
            return -1;
        }

        /** @var Stats $stats */
        $stats = $player->getStats()->first();
        if (!$stats) {
            return -1;
        }

        $tanksStatsSummary = $this->tanksStatsRepository->getSummaryTanksData($player);

        return $this->calculateTankEfficiency([
            'battles' => $tanksStatsSummary['battles'],
            'damage_dealt' => $tanksStatsSummary['damage_dealt'],
            'frags' => $tanksStatsSummary['frags'],
            'spotted' => $tanksStatsSummary['spotted'],
            'capture_points' => $tanksStatsSummary['capture_points'],
            'dropped_capture_points' => $tanksStatsSummary['dropped_capture_points']
        ], $avgTier);
    }

    #[Pure]
    public function calculateTankEfficiency(array $tankStats, int $tier): float|int
    {
        $battles = $tankStats['battles'];

        $avg_damage = $this->division($tankStats['damage_dealt'], $battles);
        $avg_frags = $this->division($tankStats['frags'], $battles);
        $avg_spotted = $this->division($tankStats['spotted'], $battles);
        $avg_capture_points = $this->division($tankStats['capture_points'], $battles);
        $avg_dropped_points = $this->division($tankStats['dropped_capture_points'], $battles);

        return $this->round(
            ($avg_damage * (10 / ($tier + 2)) * (0.23 + 2 * $tier / 100)) +
            ($avg_frags * 250) +
            ($avg_spotted * 150) +
            (log($avg_capture_points + 1) / log(1.732) * 150) +
            ($avg_dropped_points * 150)
        );
    }

    public function clanPlayersWN8(Clan $clan): array
    {
        $tanksExpected = $this->expTanksRepository->getLastTankStats();
        $clanPlayers = $this->playerRepository->getPlayerIdsFromClan($clan);
        //i[player_id][tank_id]
        $playersTanksStats = $this->tanksStatsRepository->getTanksStatsForPlayers(array_keys($clanPlayers));

        $sum = 0;
        $total = 0;

        $output = [
            'players' => []
        ];

        foreach ($playersTanksStats as $playerId => $playerTanksStats) {
            $WN8 = $this->calculateWN8ForPlayer($playerTanksStats, $tanksExpected, $clanPlayers[$playerId]['battles']);

            $output['players'][$playerId] = $WN8;

            if ($WN8 > 0) {
                $sum += $WN8;
                $total++;
            }
        }

        return array_merge($output, ['total' => $total, 'sum' => $sum]);
    }

    public function playerWN8(Player $player, &$statistics = ['battles' => 0, 'wins' => 0, 'hits' => 0, 'shots' => 0]): float|int
    {
        $tanksExpected = $this->expTanksRepository->getLastTankStats();
        $playerTanksStats = $this->getPlayerTanksStats($player);

        if (!is_array($playerTanksStats)) {
            return -1;
        }

        if ($player->getStats()->count() <= 0) {
            return -1;
        }

        /** @var Stats $playerStats */
        $playerStats = $player->getStats()->first();

        $battles = ArrayHelper::multidimensionalSum($playerTanksStats, 'battles');

        $statistics = [
            'battles' => $battles,
            'wins' => $playerStats->getWins(),
            'hits' => $playerStats->getHits(),
            'shots' => $playerStats->getShots()
        ];

        return $this->calculateWN8ForPlayer($playerTanksStats, $tanksExpected, $battles);
    }

    public function tankWN8(Tank $tank): float|int
    {
        $tankExpected = $this->expTanksRepository->getTankStatByTag($tank->getTag())[0];

        $playersTanksStats = $this->tanksStatsRepository->getTanksStatsForTankId($tank->getId());

        $playersIds = ArrayHelper::getFromMultidimensional('player_id', $playersTanksStats);
        $playersBattles = $this->statsRepository->getPlayersBattles($playersIds);

        $avg = new AvgCollection();

        foreach ($playersTanksStats as $tankStat) {
            $playerBattleItem = $playersBattles[$tankStat['player_id']];
            $result = $this->calculateTankWN8($tankStat, $tankExpected, $playerBattleItem['battles']);

            $avg->add($result->getWn());
        }

        return $avg->calculate();
    }

    public function getTanksAllStatistics(array $tanksStats, Player $player): array
    {
        return $this->calculateTanksStatsForPlayer($tanksStats, $player, 'tank_tag');
    }

    private function calculateTanksStatsForPlayer(array $tanksStats, Player $player, string $tank_key = 'tank_id'): array
    {
        $tanksExpected = $this->expTanksRepository->getLastTankStats();

        /** @var Stats $playerStats */
        $playerStats = $player->getStats()->first();

        $output = [];
        foreach ($tanksStats as $tankTag => $tankStat) {
            $tankExpected = $tanksExpected[$tankTag] ?? null;
            if (null === $tankExpected) {
                $output[$tankStat['tank_tag']] = [
                    'wn8' => -1,
                    'weight' => 0,
                    'efficiency' => -1,
                    'wn7' => -1,
                    'error' => 'no.expected',
                ];
                continue;
            }

            $wn7 = $this->calculateTankWN7($tankStat, $tankExpected['tier'], $playerStats->getBattles());
            $wn8 = $this->calculateTankWN8($tankStat, $tankExpected, $playerStats->getBattles());
            $efficiency = $this->calculateTankEfficiency($tankStat, $tankExpected['tier']);

            $output[$tankStat[$tank_key]] = [
                'wn8' => $wn8->getWn(),
                'weight' => $wn8->getWeight(),
                'efficiency' => $efficiency,
                'wn7' => $wn7->getWn()
            ];
        }

        return $output;
    }

    public function calculateWn8ForPlayerTank(int $player_id, int $tank_id)
    {
        $player = $this->playerRepository->find($player_id);
        $tankStat = $this->tanksStatsRepository->getTanksStatsForPlayers($player->getId(), $tank_id);

        $tankExpected = $this->expTanksRepository->getTankStatByTag($tankStat['tank_tag'])[0];

        //2,024.06
        return $this->calculateTankWN8($tankStat, $tankExpected, $tankStat['battles']);
    }

    public function calculatePlayerAllTanksStatistics(Player $player): array
    {
        $playerTanksStats = $this->getPlayerTanksStats($player);

        return $this->calculateTanksStatsForPlayer($playerTanksStats, $player);
    }
}