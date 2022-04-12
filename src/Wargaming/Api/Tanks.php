<?php

namespace App\Wargaming\Api;

use App\Wargaming\ApiResponse;

class Tanks
{
    use ApiTrait;

    /**
     * @param int $account_id
     * @param array $tank_ids
     * @param array $fields
     * @return ApiResponse
     */
    public function getUserTankStats(int $account_id, array $tank_ids = [], array $fields = []): ApiResponse
    {
        return $this->api->get('tanks/stats', [
            'account_id' => $account_id,
            'tank_id' => $tank_ids,
            'fields' => $fields
        ]);
    }

    /**
     * @param array $accounts_ids
     * @param array $fields
     * @return ApiResponse[]
     */
    public function getUsersTanksStats(array $accounts_ids, array $fields = []): array
    {
        $data = [];
        foreach ($accounts_ids as $account_id) {
            $data[] = ['account_id' => $account_id, 'fields' => $fields];
        }

        return $this->api->multiple('tanks/stats', $data);
    }

    /**
     * @param int $account_id
     * @param array $tank_ids
     * @return ApiResponse
     */
    public function getUserTankAchievements(int $account_id, array $tank_ids = []): ApiResponse
    {
        return $this->api->get('tanks/achievements', [
            'account_id' => $account_id,
            'tank_id' => $tank_ids
        ]);
    }
}