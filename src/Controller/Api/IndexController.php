<?php

namespace App\Controller\Api;

use App\Factory\TankFactory;
use App\Repository\TankRepository;
use App\Trait\Controller\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IndexController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private HttpClientInterface $client,
        private TankFactory         $tankFactory,
        private TankRepository      $tankRepository
    )
    {
    }

//    #[Route('/public/upload', name: 'data_upload')]
//    public function upload(Request $request): Response
//    {
//        $fileUploader = new FileUploader('/data/upload/');
//        $file = $request->files->get('file');
//        $fileUploader->upload($file);
//
//        return $this->jsonResponse([
//            'result' => true,
//            'message' => 'Plik przesłano'
//        ]);
//    }

//    #[Route('/test', name: 'test')]
//    public function test(): Response
//    {
////        $links = WotStatsParserHelper::getTanksLinks($this->client, 'https://console.worldoftanks.com/en/encyclopedia/vehicles/');
//        $tank_details = WotStatsParserHelper::tankJson($this->client, "https://console.worldoftanks.com/en/encyclopedia/vehicles/G16_PzVIB_Tiger_II/");
//    }

//    #[Route('/test2', name: 'T92_HMC')]
//    public function T92_HMC(): Response
//    {
//        $tank_details = WotStatsParserHelper::tankJson($this->client, "https://console.worldoftanks.com/en/encyclopedia/vehicles/A38_T92/");
//
//        $existing_tanks = $this->tankRepository->getTanksWithIdKey();
//        $this->tankFactory->updateStatistics($tank_details, $existing_tanks);
//        return new Response('');
//    }

//    #[Route('/command/update_players', name: 'command_update_players')]
//    public function command_update_players(): Response
//    {
//        $this->commandService->doCommand('wg:update:players');
//        return new Response('wg:update:players:done');
//    }
}
