<?php

namespace App\Controller\Api\Public;

use App\Helpers\Calculations;
use App\Repository\TankRepository;
use App\Trait\Controller\ApiResponseTrait;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TankController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private TankRepository        $tankRepository,
        private Calculations          $calculations,
        private ParameterBagInterface $parameterBag,
    )
    {
    }

    #[Route('/public/tank', name: 'data_search_tank')]
    public function tank(Request $request): Response
    {
        $tank_id = $request->get('tank_id');
        $tank = $this->tankRepository->find($tank_id);

        return $this->normalizedJsonResponse([
            'tank' => $tank,
            'wn8' => -1 //$this->calculations->tankWN8($tank)
        ], ['tank_with_tank_exp', 'tank_update_version']);
    }

    #[Route('/public/tanks', name: 'data_tanks')]
    public function tanks(PaginatorInterface $paginator, Request $request): Response
    {
        $tanks = $this->tankRepository->getAllTanksQuery($request);

        $pagination = $paginator->paginate(
            $tanks,
            $request->get('page', 1),
            $this->parameterBag->get('tanks_per_page')
        );

        return $this->normalizedPagination($pagination, ['g_tanks_base_list']);
    }
}