<?php

namespace App\Wargaming;

use JetBrains\PhpStorm\Pure;

class ResponseExtractor
{
    private array $response_data;

    public function __construct($response_data)
    {
        if (empty($response_data) || !is_array($response_data)) {
            $this->response_data = [];
        } else {
            $this->response_data = $response_data;
        }
    }

    #[Pure]
    public function first(): ResponseExtractor
    {
        if (!is_array($this->response_data)) {
            return new ResponseExtractor([]);
        }

        return new ResponseExtractor(current($this->response_data));
    }

    public function get(): array
    {
        return $this->response_data;
    }

    public function __get(string $name)
    {
        return $this->response_data[$name] ?? null;
    }
}