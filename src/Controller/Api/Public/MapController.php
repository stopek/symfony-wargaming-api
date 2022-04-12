<?php

namespace App\Controller\Api\Public;

use App\Repository\MapGeneratorRepository;
use App\Repository\MapRepository;
use App\Trait\Controller\ApiResponseTrait;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private MapRepository          $mapRepository,
        private MapGeneratorRepository $mapGeneratorRepository
    )
    {
    }

    #[Route('/public/maps', name: 'data_maps')]
    public function maps(): Response
    {
        $maps = $this->mapRepository->findAll();

        return $this->normalizedJsonResponse($maps);
    }

    #[Route('/public/maps/generator', name: 'data_map_generator')]
    public function generator(): Response
    {
        $maps = $this->mapGeneratorRepository->findBy([], ['position' => 'asc']);

        return $this->normalizedJsonResponse([
            'maps' => $maps,
            'date' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
    }

}