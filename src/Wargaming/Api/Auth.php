<?php

namespace App\Wargaming\Api;

use App\Wargaming\ApiResponse;

class Auth
{
    use ApiTrait;

    public function checkLogin(string $access_token): ApiResponse
    {
        return $this->api->get('auth/prolongate', [
            'access_token' => $access_token
        ]);
    }

    public function logOut(string $access_token): ApiResponse
    {
        return $this->api->get('auth/logout', [], [
            'access_token' => $access_token,
        ]);
    }

}