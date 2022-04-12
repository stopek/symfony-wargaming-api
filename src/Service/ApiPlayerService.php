<?php

namespace App\Service;

use App\Entity\Clan;
use App\Entity\Player;
use App\Factory\PlayerFactory;
use App\Factory\StatsFactory;
use App\Factory\TanksStatsFactory;
use App\Repository\PlayerRepository;
use App\Wargaming\Api\Account;
use App\Wargaming\Api\Tanks;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Console\Output\OutputInterface;

class ApiPlayerService
{
    public function __construct(
        private PlayerFactory     $playerFactory,
        private StatsFactory      $statsFactory,
        private PlayerRepository  $playerRepository,
        private TanksStatsFactory $tanksStatsFactory
    )
    {
    }

    /**
     * @param Clan[] $clans_collection
     * @param array $api_response_clans
     * @param array $existing_players
     */
    public function createPlayersFromClanMembers(array $clans_collection, array $api_response_clans, array $existing_players = [])
    {
        foreach ($clans_collection as $item) {
            if (null === ($members = $api_response_clans[$item->getId()]['members'] ?? null)) {
                continue;
            }

            $this->playerFactory->createFromClanMembers($members, $item, $existing_players);
        }
    }

    /**
     * @param array $clans_collection
     */
    public function updatePlayersRoleFromClanMembers(array $clans_collection)
    {
        $data = [];

        foreach ($clans_collection as $item) {
            foreach ($item['members'] as $member) {
                $data[$member['role']][] = $member['account_id'];
            }
        }

        $this->playerRepository->updatePlayersForRole($data);
    }

    /**
     * @param Clan[] $clans
     */
    public function unpinPlayersFromClans(array $clans = [], array $response_clans = [])
    {
        $players_ids = [];
        foreach ($clans as $clan) {
            if ($clan->isClanUnpinPlayers()) {
                $players_ids = array_merge($players_ids, $clan->getPlayers()->map(function (Player $player) {
                    return $player->getId();
                })->toArray());

                continue;
            }

            $clan_response = $response_clans[$clan->getId()] ?? null;
            if (!$clan_response) {
                continue;
            }

            $players = $clan->getPlayers()->map(function (Player $player) {
                return $player->getId();
            })->toArray();

            $diff = array_diff(
                $players,
                $clan_response['members_ids']
            );

            $players_ids = array_merge($players_ids, $diff);
        }

        $this->playerRepository->unpinPlayersFromClan($players_ids);
    }

    #[ArrayShape(['message' => "string"])]
    public function setPlayersInactive(): array
    {
        $players = $this->playerRepository->setPlayersInactive();
        return ['message' => $players . ' graczy zostało ustawionych jako nieaktywni'];
    }

    #[ArrayShape(['message' => "string"])]
    public function checkPlayersActivity(Account $account): array
    {
        $players = $this->playerRepository->getPlayersToCheckActivity(1000);
        $players_ids = array_keys($players);

        $player_to_update = [];
        $nothing_change_players = [];

        $chunk_ids = array_chunk($players_ids, 100);
        foreach ($chunk_ids as $ids) {
            $response = $account->getPlayersPersonalData($ids, ['last_battle_time']);

            if (!$response->isEmptyResponse()) {
                $response_data = $response->getResponse()->get();

                foreach ($response_data as $account_id => $player) {
                    $database_player = $players[$account_id] ?? null;

                    if ($database_player->canUpdateLastBattlePlayer($player['last_battle_time'])) {
                        $player_to_update[$account_id] = $database_player;
                        continue;
                    }

                    $nothing_change_players[$account_id] = $database_player;
                }
            }
        }

        $this->playerFactory->setActivePlayers($player_to_update);
        $this->playerFactory->setStillInactivePlayers($nothing_change_players);

        return ['message' => 'Aktywność graczy została zaktualizowana. '
            . count($player_to_update) . ' graczy zmienia swoją aktywność, '
            . count($nothing_change_players) . ' graczy wciąż bez zmian'
        ];
    }

    #[ArrayShape(['message' => "string"])]
    public function updatePlayersStatistics(Account $account, Tanks $tanks, OutputInterface $output, bool $only_online = false): array
    {
        $locked = $this->playerRepository->countLockedPlayers($only_online);
        if (isset($locked[0])) {
            return ['message' => 'Aktualizacja graczy zablokowana'];
        }

        $chunk = 100;

        $players = $this->playerRepository->getPlayerToUpdate($only_online, 500);

        $players_ids = array_keys($players);

        if (0 === count($players_ids)) {
            return ['message' => 'Brak graczy do aktualizacji'];
        }

        $this->playerRepository->lockPlayers($players);

        $updates_ids = [];
        $nothing_change_players = [];
        $deleted_players = [];

        $chunk_ids = array_chunk($players_ids, $chunk);

        foreach ($chunk_ids as $ids) {
            $response = $account->getPlayersPersonalData($ids, ['updated_at']);

            if (!$response->isEmptyResponse()) {
                $response_data = $response->getResponse()->get();

                foreach ($response_data as $account_id => $player) {
                    $database_player = $players[$account_id] ?? null;

                    if (null === $database_player) {
                        continue;
                    }

                    if (null === $player) {
                        $deleted_players[$account_id] = $database_player;
                        continue;
                    }

                    if ($database_player->canUpdatePlayer($player['updated_at'])) {
                        $updates_ids[$account_id] = $database_player;
                    } else {
                        $nothing_change_players[$account_id] = $database_player;
                    }
                }
            }
        }

        $output->writeln("Gracze gdzie nie robimy nic");
        $output->writeln(join(', ', array_keys($nothing_change_players)));
        $this->playerFactory->updateNothingChange($nothing_change_players);

        $output->writeln("Gracze, których usuwamy");
        $output->writeln(join(', ', array_keys($deleted_players)));
        $this->playerFactory->deletePlayers($deleted_players);

        if (0 === count($updates_ids)) {
            return ['message' => 'Wczytani gracze nie wymagają aktualizacji'];
        }

        $output->writeln("Gracze do sprawdzenia szczegółów");
        $output->writeln(join(', ', array_keys($updates_ids)));

        $chunk_ids = array_chunk($updates_ids, $chunk, true);

        foreach ($chunk_ids as $ids) {
            $part_user_tanks_stats = $tanks->getUsersTanksStats(array_keys($ids), [
                'all', 'last_battle_time', 'max_xp', 'trees_cut',
                'max_frags', 'mark_of_mastery', 'battle_life_time',
                'tank_id', 'account_id'
            ]);

            $output->writeln("Aktualizujemy statystyki czołgów paczki graczy");
            $output->writeln(join(', ', array_keys($ids)));

            $this->tanksStatsFactory->createOrUpdate($ids, $part_user_tanks_stats);
        }

        foreach ($chunk_ids as $ids) {
            $response = $account->getPlayersPersonalData(array_keys($ids));
            $response_data = $response->getResponse()->get();

            $output->writeln("Aktualizujemy statystyki ogólne paczki graczy");
            $output->writeln(join(', ', array_keys($ids)));

            $this->statsFactory->createOrUpdate($ids, $response_data);

            $this->playerFactory->update($ids, $response_data);
        }

        return ['message' => 'Aktualizacja graczy ukończona'];
    }
}