<?php

namespace App\Controller\Api\Public;

use App\Repository\ExpTanksRepository;
use App\Repository\ExpUpdateRepository;
use App\Trait\Controller\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpectedController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private ExpUpdateRepository $expUpdateRepository,
        private ExpTanksRepository  $expTanksRepository,
    )
    {
    }

    #[Route('/public/expected', name: 'data_expected_display')]
    public function expected(Request $request): Response
    {
        $current_update = $this->expUpdateRepository->getLastUpdateVersion();
        $tanks = $this->expTanksRepository->getLastTankStatsWithTanks($current_update['id'], $request);

        return $this->normalizedJsonResponse([
            'update' => $current_update,
            'tanks' => $tanks
        ], ['expected_tanks']);
    }
}
