<?php

namespace App\Wargaming\Api;

trait ApiTrait
{
    public function __construct(public ApiInterface $api)
    {
    }
}