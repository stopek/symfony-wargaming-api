<?php

namespace App\Factory;

use App\Collection\EntityCollection;
use App\Entity\PlayerStatsHistory;
use App\Repository\PlayerStatsHistoryRepository;

class PlayerStatsHistoryFactory
{
    public function __construct(private PlayerStatsHistoryRepository $playerStatsHistoryRepository)
    {
    }

    public function store(array $list = [])
    {
        $collection = new EntityCollection();

        foreach ($list as $statistic) {
            $object = new PlayerStatsHistory();
            $object
                ->setWn8($statistic['wn8'])
                ->setPlayer($statistic['player'])
                ->setBattles($statistic['battles']);

            $collection->offsetSet('', $object);
        }

        $this->playerStatsHistoryRepository->saveMultiple($collection);
    }
}