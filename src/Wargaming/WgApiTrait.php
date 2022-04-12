<?php

namespace App\Wargaming;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait WgApiTrait
{
    private HttpClientInterface $client;

    private ?string $application_id;
    private ?string $language;

    /**
     * @param string $path
     * @param array $data
     * @return ResponseInterface|null
     */
    public function createResponse(string $path, array $data = [], array $post = []): ?ResponseInterface
    {
        $data = array_filter($data, function ($item) {
            return !empty($item);
        });

        $data = array_map(function ($item) {
            return is_array($item) ? join(',', $item) : $item;
        }, $data);

        try {
            return $this->client->request(
                'POST',
                $this->getApiUrl() . '/' . $path . '/',
                [
                    'query' => array_merge([
                        'application_id' => $this->application_id,
                        'language' => $this->language
                    ], $data),
                    'body' => $post
                ]
            );
        } catch (TransportExceptionInterface) {
            return null;
        }
    }

    public function getApiUrl(): string
    {
        return $this->url;
    }
}