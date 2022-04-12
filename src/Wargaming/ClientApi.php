<?php

namespace App\Wargaming;

use App\Wargaming\Api\ApiInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;

class ClientApi implements ApiInterface
{
    use ApiTrait;
    use WgApiTrait;

    private string $url = 'https://api-console.worldoftanks.com/wotx';

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->application_id = $parameterBag->get('wg_public_api_key');
        $this->language = 'en';
        $this->client = HttpClient::create();
    }
}