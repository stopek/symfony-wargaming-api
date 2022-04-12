<?php

namespace App\Service;

use App\Factory\ClanFactory;
use App\Helpers\ArrayHelper;
use App\Repository\ClanRepository;
use App\Repository\PlayerRepository;
use App\Wargaming\Api\Clan;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiClanService
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private ClanFactory           $clanFactory,
        private ClanRepository        $clanRepository,
        private ApiPlayerService      $playerService,
        private PlayerRepository      $playerRepository
    )
    {
    }

    public function clansCreator(Clan $clan, int $page = 1): array
    {
        $existing_clans = ArrayHelper::getFromMultidimensional('id', $this->clanRepository->getClanIds());
        return $this->create($clan, $page, array_flip($existing_clans));
    }

    private function create(Clan $clan, int $page, array $existing_clans = []): array
    {
        $page = max(1, $page);

        $done = ['message' => 'Clan creation is complete'];

        $minimum_members = $this->parameterBag->get('min_clan_players');

        $response = $clan->clans($page, [
            'members_count', 'name', 'created_at', 'tag', 'clan_id'
        ]);

        $clan->clanDetails($clan_base->clan_id ?? 0);
        if ($response->isEmptyResponse()) {
            return $done;
        }

        $response_data = $response->getResponse()->get();
        if ($response_data[0]['members_count'] < $minimum_members) {
            return $done;
        }

        $this->clanFactory->create($response_data, $minimum_members, $existing_clans);

        return $this->create($clan, $page + 1, $existing_clans);
    }

    #[ArrayShape(['message' => "string"])]
    public function clansUpdater(Clan $clan): array
    {
        $existing_players = $this->playerRepository->getAllPlayersIds();
        $done = ['message' => 'Clan updates is completes'];

        $list = $this->clanRepository->getClansToUpdateWithIdKeys();
        $ids = array_keys($list);

        if (count($ids) === 0) {
            return $done;
        }

        $response = $clan->clansDetails($ids, ['members']);
        if ($response->isEmptyResponse()) {
            return $done;
        }

        $response_data = $response->getResponse()->get();

        $this->clanFactory->update($list, $response_data);
        $this->playerService->createPlayersFromClanMembers($list, $response_data, $existing_players);
        $this->playerService->unpinPlayersFromClans($list, $response_data);
        $this->playerService->updatePlayersRoleFromClanMembers($response_data);

        return $done;
    }
}