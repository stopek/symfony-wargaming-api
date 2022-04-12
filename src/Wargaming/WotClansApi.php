<?php

namespace App\Wargaming;

use App\Wargaming\Api\ApiInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class WotClansApi implements ApiInterface
{
    use ApiTrait;

    private const API_STATUS_OK = 1;
    private const API_STATUS_ERROR = 2;

    private HttpClientInterface $client;

    private string $url = 'https://wotclans.com.br/api';

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    /**
     * @param string $path
     * @param array $data
     * @return ResponseInterface|null
     */
    public function createResponse(string $path, array $data = []): ?ResponseInterface
    {
        try {
            return $this->client->request(
                'GET',
                $this->url . "/" . $path
            );
        } catch (TransportExceptionInterface) {
            return null;
        }
    }
}