<?php

namespace App\Wargaming;

use JetBrains\PhpStorm\Pure;

class ApiResponse
{
    private ?string $message;
    private ?string $type;

    public function __construct(private array $response = [], private int $status = 0)
    {
    }

    public function setResponse(array $response)
    {
        $this->response = $response;
    }

    #[Pure]
    public function getResponse(): ResponseExtractor
    {
        return new ResponseExtractor(empty($this->response) ? [] : $this->response);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function isEmptyResponse(): bool
    {
        return empty($this->response) || count($this->response) === 0;
    }
}