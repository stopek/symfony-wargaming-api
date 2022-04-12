<?php

namespace App\Controller\Api\Public;

use App\Factory\SearchFactory;
use App\Helpers\Calculations;
use App\Repository\ClanRepository;
use App\Trait\Controller\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ClanController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private ClanRepository        $clanRepository,
        private ParameterBagInterface $parameterBag,
        private Calculations          $calculations,
        private SearchFactory         $searchFactory
    )
    {
    }

    #[Route('/public/clans/list', name: 'data_clans')]
    public function list(Request $request): Response
    {
        $total = $this->clanRepository->getClansTotalItems($request);
        $page = $request->get('page', 1);
        $perPage = $this->parameterBag->get('clans_per_page');

        $clans = $this->clanRepository->getClansList($request, $page, $perPage);

        $this->searchFactory->search('clan', $request->get('clan_name'));

        return $this->normalizedCustomPagination($clans, ['clan_base'], [
            'total' => $total,
            'current' => $page,
            'perPage' => $perPage
        ]);
    }

    #[Route('/public/clan/search', name: 'data_search_clan')]
    public function search(Request $request): Response
    {
        $clan_name = $request->get('clan_name');
        $clan = $this->clanRepository->getClanByTagOrdId($clan_name);

        if (($response = $this->notFoundIfNull($clan)) instanceof Response) {
            return $response;
        }

        $wn8Calculations = $this->calculations->clanPlayersWN8($clan);
        $wn7Calculations = $this->calculations->clanPlayersWN7($clan);
        $efficiencyCalculations = $this->calculations->clanPlayersEfficiency($clan);

        $response_data = [
            'clan' => $clan,
            'statistics' => [
                'players' => [
                    'wn8' => $wn8Calculations['players'],
                    'wn7' => $wn7Calculations['players'],
                    'efficiency' => $efficiencyCalculations['players']
                ],
                'wn8' => $this->calculations->clanWN($wn8Calculations),
                'wn7' => $this->calculations->clanWN($wn7Calculations),
                'efficiency' => $this->calculations->clanWN($efficiencyCalculations),
            ]
        ];

        $this->searchFactory->clan($clan_name, $clan);

        return $this->normalizedJsonResponse($response_data);
    }

}