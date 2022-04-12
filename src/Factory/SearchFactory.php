<?php

namespace App\Factory;

use App\Entity\Clan;
use App\Entity\Player;
use App\Entity\Search;
use App\Repository\SearchRepository;

class SearchFactory
{
    public function __construct(private SearchRepository $searchRepository)
    {
    }

    public function clan(?string $clanName, ?Clan $clan): void
    {
        $search = new Search();
        $search
            ->setClan($clan)
            ->setString($clanName);

        $this->searchRepository->store($search);
    }

    public function player(?string $playerName, ?Player $player): void
    {
        $search = new Search();
        $search
            ->setPlayer($player)
            ->setString($playerName);

        $this->searchRepository->store($search);
    }

    public function search(string $type, ?string $searchName): void
    {
        if (empty($searchName)) {
            return;
        }

        $search = new Search();
        $search
            ->setString($type . '|' . $searchName);

        $this->searchRepository->store($search);
    }
}