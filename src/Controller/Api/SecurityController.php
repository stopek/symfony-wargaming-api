<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Factory\PlayerFactory;
use App\Helpers\ArrayHelper;
use App\Helpers\Calculations;
use App\Repository\PlayerRepository;
use App\Repository\TanksStatsRepository;
use App\Repository\UserRepository;
use App\Trait\Controller\ApiResponseTrait;
use App\Wargaming\Api\Auth;
use App\Wargaming\ClientApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private Calculations         $calculations,
        private PlayerRepository     $playerRepository,
        private TanksStatsRepository $tanksStatsRepository,
        private PlayerFactory        $playerFactory,
        private UserRepository       $userRepository,
        private ClientApi            $api
    )
    {
    }

    #[Route('/security/log_out_account', name: 'security_log_out_account')]
    public function log_out_account(Request $request): Response
    {
        $userEntity = $this->userRepository->find($request->get('id'));
        if (null === $userEntity) {
            return $this->unauthenticated();
        }

        /** @var User $user */
        $user = $this->getUser();
        $player = $user->getPlayer();

        if ($userEntity->getPlayer() !== $player) {
            return $this->unauthenticated();
        }

        (new Auth($this->api))->logOut($userEntity->getWgApiToken());
        $this->userRepository->logOutByToken($userEntity->getApiToken());

        return $this->jsonResponse(['message' => 'success.logout']);
    }

    #[Route('/security/logout', name: 'data_logout')]
    public function logout(Request $request): Response
    {
        if ($request->headers->has('X-Auth-Token')) {
            $token = $request->headers->get('X-Auth-Token');
            $user = $this->userRepository->getUserToLogIn($token);
            if (null !== $user) {
                (new Auth($this->api))->logOut($user->getWgApiToken());
            }

            $this->userRepository->logOutByToken($token);
            return $this->jsonResponse(['message' => 'success.logout']);
        }

        return new Response('', 401);
    }


    #[Route('/security/get_user', name: 'security_get_user')]
    public function security_get_user(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $player = $user->getPlayer();

        if (!$player) {
            return $this->unauthenticated();
        }

        $this->playerRepository->updateOnlineDate($player);

        $tanksStatistics = $this->calculations->calculatePlayerAllTanksStatistics($player);
        $recently = $this->tanksStatsRepository->getRecentlyPlayedTanks($player, 12);
        $stats = $this->calculations->getTanksAllStatistics($recently, $player);

        $normalized_player = $this->normalize($player, ['auth_user', 'player_details', 'g_player_stats_history']);
        $statsAccessPath = ArrayHelper::multidimensionalSetKeyViaAccessPath($normalized_player['tanksStats'], '[tank][id]');
        $normalized_player['tanksStats'] = ArrayHelper::multidimensionalMerge($tanksStatistics, $statsAccessPath);

        return $this->jsonResponse([
            'session_id' => $user->getId(),
            'player' => $normalized_player,
            'statistics' => [
                'wn8' => $this->calculations->playerWN8($player),
                'wn7' => $this->calculations->playerWN7($player),
                'efficiency' => $this->calculations->playerEfficiency($player)
            ],
            'recently' => array_merge_recursive($recently, $stats),
            'roles' => $user->getRoles()
        ]);
    }
}
