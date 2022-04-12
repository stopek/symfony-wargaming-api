<?php

namespace App\Controller\Api\Public;

use App\Repository\MoeTanksRepository;
use App\Repository\MoeUpdateRepository;
use App\Trait\Controller\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoeController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private MoeUpdateRepository $moeUpdateRepository,
        private MoeTanksRepository  $moeTanksRepository,
    )
    {
    }

    #[Route('/public/moe', name: 'data_moe_display')]
    public function moe(): Response
    {
        $current_update = $this->moeUpdateRepository->getLastUpdateVersion();
        $tanks = $this->moeTanksRepository->getLastTankStatsWithTanks($current_update['id']);

        return $this->normalizedJsonResponse([
            'update' => $current_update,
            'tanks' => $tanks
        ], ['moe_tanks']);
    }
}
