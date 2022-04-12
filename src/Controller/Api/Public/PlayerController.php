<?php

namespace App\Controller\Api\Public;

use App\Entity\Player;
use App\Factory\SearchFactory;
use App\Helpers\ArrayHelper;
use App\Helpers\Calculations;
use App\Repository\PlayerRepository;
use App\Trait\Controller\ApiResponseTrait;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private PlayerRepository $playerRepository,
        private Calculations     $calculations,
        private SearchFactory    $searchFactory
    )
    {
    }

    #[Route('/public/player/search', name: 'data_search_player')]
    public function search(PaginatorInterface $paginator, Request $request): Response
    {
        $player_name = $request->get('player_name');
        $players = $this->playerRepository->getPlayersByNameQuery($player_name);

        $pagination = $paginator->paginate(
            $players,
            $request->get('page', 1),
            20
        );

        $this->searchFactory->search($player_name, 'player');

        return $this->normalizedPagination($pagination, ['g_players_list', 'g_player_clan_base_info', 'g_players_list_base_stats'], [
            'statistics' => [
                'wn8' => $this->calculations->playersWN8($pagination->getItems()),
                'wn7' => $this->calculations->playersWN7($pagination->getItems()),
                'efficiency' => $this->calculations->playersEfficiency($pagination->getItems())
            ]
        ]);
    }

    #[Route('/public/player/profile', name: 'data_search_player_by_id')]
    public function profile(Request $request): Response
    {
        $account_id = $request->get('account_id');
        $player = $this->playerRepository->getPlayerById($account_id);

        $this->searchFactory->player($account_id, $player);

        return $this->player($player);
    }

    /**
     * @param Player|null $player
     * @return Response
     */
    private function player(?Player $player): Response
    {
        if (($response = $this->notFoundIfNull($player)) instanceof Response) {
            return $response;
        }

        $tanksStatistics = $this->calculations->calculatePlayerAllTanksStatistics($player);
        $normalized_player = $this->normalize($player, ['player_details', 'g_player_clan_base_info']);
        $statsAccessPath = ArrayHelper::multidimensionalSetKeyViaAccessPath($normalized_player['tanksStats'], '[tank][id]');

        $normalized_player['tanksStats'] = ArrayHelper::multidimensionalMerge($tanksStatistics, $statsAccessPath);

        $player_response = [
            'player' => $normalized_player,
            'statistics' => [
                'wn8' => $this->calculations->playerWn8($player),
                'wn7' => $this->calculations->playerWN7($player),
                'efficiency' => $this->calculations->playerEfficiency($player),
                'tanks_wn8' => $tanksStatistics
            ]
        ];

        return $this->jsonResponse($player_response);
    }

    #[Route('/public/player/tank_info', name: 'data_tank_info')]
    public function player_tank_wn8(Request $request): Response
    {
        $player = $this->playerRepository->getPlayerById($request->get('account_id'));
        dump($this->calculations->playerWN7($player));

//        dump($this->calculations->calculateWn8ForPlayerTank(
//            $request->get('account_id'),
//            $request->get('tank_id')
//        ));

        exit;
    }

}