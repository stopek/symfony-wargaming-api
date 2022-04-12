<?php

namespace App\Service;

use App\Entity\PlayerStatsHistory;
use App\Helpers\Calculations;
use App\Repository\PlayerRepository;
use App\Repository\PlayerStatsHistoryRepository;
use DateTimeInterface;
use JetBrains\PhpStorm\ArrayShape;

class PlayersHistoryService
{
    public function __construct(
        private PlayerRepository             $playerRepository,
        private Calculations                 $calculations,
        private PlayerStatsHistoryRepository $playerStatsHistoryRepository
    )
    {
    }

    #[ArrayShape(['message' => "string"])]
    public function saveUserOverallStatistics(int $limit, DateTimeInterface $updateDate): array
    {
        $players = $this->playerRepository->getPlayersToSaveHistory($limit, $updateDate);

        if (!isset($players[0])) {
            return ['message' => 'Brak graczy do aktualizacji'];
        }

        $this->playerRepository->lockHistoryPlayers($players, true);

        foreach ($players as $player) {
            $wn8 = $this->calculations->playerWN8($player, $statistics);
            $efficiency = $this->calculations->playerEfficiency($player);
            $wn7 = $this->calculations->playerWN7($player);

            $history = new PlayerStatsHistory();
            $history
                ->setPlayer($player)
                ->setWn7($wn7 !== -1 ? $wn7 : null)
                ->setWn8($wn8 !== -1 ? $wn8 : null)
                ->setEfficiency($efficiency !== -1 ? $efficiency : null)
                ->setBattles($statistics['battles'])
                ->setWinRatio($this->calculations->division($statistics['wins'], $statistics['battles']))
                ->setDamageRatio($this->calculations->division($statistics['hits'], $statistics['shots']));

            $this->playerStatsHistoryRepository->store($history);
        }

        $this->playerRepository->lockHistoryPlayers($players, false);

        return ['message' => 'Aktualizacja graczy ukończona'];
    }
}