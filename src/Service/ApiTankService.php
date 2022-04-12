<?php

namespace App\Service;

use App\Factory\TankFactory;
use App\Repository\TankRepository;
use App\Wargaming\Api\Encyclopedia;
use JetBrains\PhpStorm\ArrayShape;

class ApiTankService
{
    public function __construct(
        private TankFactory    $tankFactory,
        private TankRepository $tankRepository
    )
    {
    }

    #[ArrayShape(['message' => "string"])]
    public function createOrUpdate(Encyclopedia $encyclopedia): array
    {
        $response = $encyclopedia->getTanks();

        if ($response->isEmptyResponse()) {
            return ['message' => 'Tanks list is empty'];
        }

        $existing_tanks = $this->tankRepository->getTanksWithIdKey();
        $this->tankFactory->createOrUpdate($response->getResponse()->get(), $existing_tanks);

        return ['message' => 'Tank created or update'];
    }
}