<?php

namespace App\Factory;

use App\Collection\EntityCollection;
use App\Entity\Clan;
use App\Repository\ClanRepository;
use App\Repository\PlayerRepository;
use DateTime;

class ClanFactory
{
    public function __construct(
        private ClanRepository   $clanRepository,
        private PlayerRepository $playerRepository
    )
    {
    }

    public function create(array $clans, int $minimum_members, array $existing_clans = [])
    {
        $collection = new EntityCollection();

        foreach ($clans as $clan) {
            if ($clan['members_count'] >= $minimum_members && !isset($existing_clans[$clan['clan_id']])) {
                $clanEntity = new Clan();
                $clanEntity
                    ->setId($clan['clan_id'])
                    ->setClanCreatedAt($clan['created_at'])
                    ->setMembersCount($clan['members_count'])
                    ->setName($clan['name'])
                    ->setUpdates(0)
                    ->setTag($clan['tag']);

                $collection->offsetSet('', $clanEntity);
            }
        }

        $this->clanRepository->saveMultiple($collection);
    }

    /**
     * @param array|Clan[] $clans
     */
    public function update(array $clans, array $updates)
    {
        $collection = new EntityCollection();

        foreach ($clans as $clan) {
            $update = $updates[$clan->getId()];

            if (null !== ($creator = $this->playerRepository->find($update['creator_id']))) {
                $clan->setCreator($creator);
            }

            if (null !== ($leader = $this->playerRepository->find($update['leader_id']))) {
                $clan->setLeader($leader);
            }

            if (null === $update) {
                $clan->setDeletedAt(new DateTime());
            } else {
                if (true === $update['is_clan_disbanded']) {
                    $clan->setIsDisbanded(true);
                } else {
                    $clan
                        ->setClanUpdatedAt($update['updated_at'])
                        ->setName($update['name'])
                        ->setTag($update['tag']);
                }
            }

            $clan
                ->setMembersCount($update['members_count'] ?? 0)
                ->setUpdates($clan->getUpdates() + 1);

            $collection->offsetSet('', $clan);
        }

        $this->clanRepository->saveMultiple($collection);
    }
}