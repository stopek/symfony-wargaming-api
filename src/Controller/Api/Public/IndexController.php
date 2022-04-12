<?php

namespace App\Controller\Api\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'api')]
    public function index(ParameterBagInterface $parameterBag): Response
    {
        return $this->redirect($parameterBag->get('wg_frontend'));
    }
}
