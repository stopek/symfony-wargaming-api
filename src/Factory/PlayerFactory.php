<?php

namespace App\Factory;

use App\Collection\EntityCollection;
use App\Entity\Clan;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use DateTime;

class PlayerFactory
{
    public function __construct(private PlayerRepository $playerRepository)
    {
    }

    public function createPlayer(int $player_id, bool $is_ps_user = true): Player
    {
        $player = new Player();
        $player
            ->setId($player_id)
            ->setName($player_id . '-' . ($is_ps_user ? 'p' : 'x'))
            ->setUpdates(0);

        $this->playerRepository->save($player);

        return $player;
    }

    public function createFromClanMembers(array $members, Clan $clan, array $existing_players = [])
    {
        $collection = new EntityCollection();

        foreach ($members as $member) {
            if (isset($existing_players[$member['account_id']])) {
                continue;
            }

            $player = new Player();
            $player
                ->setIsLocked(false)
                ->setId($member['account_id'])
                ->setName($member['account_name'] ?? '')
                ->setPlayerJoinedAt($member['joined_at'])
                ->setRole($member['role'])
                ->setClan($clan)
                ->setUpdates(0);

            $collection->offsetSet('', $player);
        }

        $this->playerRepository->saveMultiple($collection);
    }

    /**
     * @param Player[] $players
     * @param array $response_player_details
     */
    public function update(array $players, array $response_player_details)
    {
        $collection = new EntityCollection();

        foreach ($players as $player) {
            $data = $response_player_details[$player->getId()];

            $player
                ->setIsLocked(false)
                ->setLockedAt(null)
                ->setName($data['nickname'])
                ->setGlobalRating($data['global_rating'])
                ->setLastBattleTime($data['last_battle_time'])
                ->setPlayerUpdatedAt($data['updated_at'])
                ->setPlayerCreatedAt($data['created_at'])
                ->setInactiveAt(null)
                ->setUpdates($player->getUpdates() + 1);

            $collection->offsetSet('', $player);
        }

        $this->playerRepository->saveMultiple($collection);
    }

    /**
     * @param Player[] $players
     */
    public function updateNothingChange(array $players)
    {
        $collection = new EntityCollection();

        foreach ($players as $player) {
            $player
                ->setIsLocked(false)
                ->setIsInactive(false)
                ->setInactiveAt(null)
                ->setUpdates($player->getUpdates() + 1);

            $collection->offsetSet('', $player);
        }

        $this->playerRepository->saveMultiple($collection);
    }

    /**
     * @param Player[] $players
     */
    public function deletePlayers(array $players)
    {
        $collection = new EntityCollection();

        foreach ($players as $player) {
            $player
                ->setIsLocked(false)
                ->setDeletedAt(new DateTime());

            $collection->offsetSet('', $player);
        }

        $this->playerRepository->saveMultiple($collection);
    }

    /**
     * @param Player[] $players
     */
    public function setActivePlayers(array $players)
    {
        $collection = new EntityCollection();

        foreach ($players as $player) {
            $player
                ->setIsInactive(false);

            $collection->offsetSet('', $player);
        }

        $this->playerRepository->saveMultiple($collection);
    }

    /**
     * @param Player[] $players
     */
    public function setStillInactivePlayers(array $players)
    {
        $collection = new EntityCollection();

        foreach ($players as $player) {
            $player
                ->setIsInactive(true)
                ->setInactiveAt(new DateTime());

            $collection->offsetSet('', $player);
        }

        $this->playerRepository->saveMultiple($collection);
    }
}