<?php

namespace App\Wargaming\Api;

use App\Wargaming\ApiResponse;

class Account
{
    use ApiTrait;

    /**
     * @param string $name
     * @return ApiResponse
     */
    public function searchPlayer(string $name): ApiResponse
    {
        return $this->api->get('account/list', [
            'search' => $name
        ]);
    }

    /**
     * @param int $account_id
     * @return ApiResponse
     */
    public function getPlayerPersonalData(int $account_id): ApiResponse
    {
        return $this->api->get('account/info', [
            'account_id' => $account_id
        ]);
    }

    /**
     * @param array $account_ids
     * @param array $fields
     * @return ApiResponse
     */
    public function getPlayersPersonalData(array $account_ids, array $fields = []): ApiResponse
    {
        return $this->api->get('account/info', [
            'account_id' => $account_ids,
            'fields' => $fields
        ]);
    }

    /**
     * @param int $account_id
     * @return ApiResponse
     */
    public function getPlayerAchievements(int $account_id): ApiResponse
    {
        return $this->api->get('account/achievements', [
            'account_id' => $account_id
        ]);
    }
}