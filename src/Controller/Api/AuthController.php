<?php

namespace App\Controller\Api;

use App\Repository\PlayerRepository;
use App\Repository\UserRepository;
use App\Trait\Controller\ApiResponseTrait;
use App\Wargaming\ClientApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(
        private UserRepository        $userRepository,
        private ParameterBagInterface $parameterBag,
        private PlayerRepository      $playerRepository,
        private ClientApi             $api
    )
    {
    }

    #[Route('/public/login_callback', name: 'data_login_callback')]
    public function login_callback(Request $request): Response
    {
        $token = $this->userRepository->createUserFromInputBag(
            $this->playerRepository,
            $request->query,
            $request->server
        );
        $redirect_to = $this->parameterBag->get('wg_frontend_login') . '?token=' . $token;

        return new RedirectResponse($redirect_to);
    }

    #[Route('/login', name: 'data_login')]
    public function login(): Response
    {
        $redirect_to =
            $this->api->getApiUrl() .
            '/auth/login/?application_id=' .
            $this->parameterBag->get('wg_public_api_key') .
            '&redirect_uri=' . $this->parameterBag->get('wg_backend_domain') .
            $this->generateUrl('data_login_callback');

        return new RedirectResponse($redirect_to);
    }
}
