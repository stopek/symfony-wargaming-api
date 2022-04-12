<?php

namespace App\Wargaming;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait ApiTrait
{
    /**
     * @param string $path
     * @param array $data
     * @return ApiResponse[]
     */
    public function multiple(string $path, array $data = []): array
    {
        $responses = [];
        foreach ($data as $request_data) {
            $responses[] = $this->createResponse($path, $request_data);
        }

        $output = [];
        foreach ($responses as $response) {
            $output[] = $this->parseResponse($response);
        }

        return $output;
    }

    /**
     * @param ResponseInterface $response
     * @return ApiResponse
     */
    public function parseResponse(ResponseInterface $response): ApiResponse
    {
        if (null === $response) {
            return $this->catchError('null_response');
        }

        try {
            $array = $response->toArray();
        } catch (ClientExceptionInterface $e) {
            return $this->catchError('client_exception', $e->getMessage());
        } catch (DecodingExceptionInterface $e) {
            return $this->catchError('decodint_exception', $e->getMessage());
        } catch (RedirectionExceptionInterface $e) {
            return $this->catchError('redirection_exception', $e->getMessage());
        } catch (ServerExceptionInterface $e) {
            return $this->catchError('server_exception', $e->getMessage());
        } catch (TransportExceptionInterface $e) {
            return $this->catchError('transport_exception', $e->getMessage());
        }

        if (!is_array($array)) {
            return $this->catchError('no_data_exists');
        }

        return new ApiResponse($array['data'] ?? $array, 1);
    }

    /**
     * @param string $type
     * @param string|null $message
     * @return ApiResponse
     */
    private function catchError(string $type, ?string $message = ''): ApiResponse
    {
        $response = new ApiResponse([], 2);
        $response->setMessage($message);
        $response->setType($type);

        return $response;
    }

    /**
     * @param string $path
     * @param array $data
     * @return ApiResponse
     */
    public function get(string $path, array $data = [], $post = []): ApiResponse
    {
        $response = $this->createResponse($path, $data, $post);

        return $this->parseResponse($response);
    }
}