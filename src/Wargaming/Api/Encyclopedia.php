<?php

namespace App\Wargaming\Api;

use App\Wargaming\ApiResponse;

class Encyclopedia
{
    use ApiTrait;

    /**
     * @param array $nations_list
     * @return ApiResponse
     */
    public function getNationsTanks(array $nations_list = []): ApiResponse
    {
        return $this->getEncyclopedia('nation', $nations_list);
    }

    /**
     * @param string $key
     * @param array $data
     * @return ApiResponse
     */
    private function getEncyclopedia(string $key, array $data): ApiResponse
    {
        return $this->api->get('encyclopedia/vehicles', [
            $key => $data
        ]);
    }

    /**
     * @param string $nation
     * @return ApiResponse
     */
    public function getNationTanks(string $nation): ApiResponse
    {
        return $this->getEncyclopedia('nation', [$nation]);
    }

    /**
     * @param array $tiers_list
     * @return ApiResponse
     */
    public function getTiersTanks(array $tiers_list = []): ApiResponse
    {
        return $this->getEncyclopedia('tier', $tiers_list);
    }

    /**
     * @param int $tier
     * @return ApiResponse
     */
    public function getTierTanks(int $tier): ApiResponse
    {
        return $this->getEncyclopedia('tier', [$tier]);
    }

    /**
     * @param int $tank_id
     * @return ApiResponse
     */
    public function getTank(int $tank_id): ApiResponse
    {
        return $this->getEncyclopedia('tank_id', [$tank_id]);
    }

    /**
     * @param array $tank_ids
     * @return ApiResponse
     */
    public function getTanks(array $tank_ids = []): ApiResponse
    {
        return $this->getEncyclopedia('tank_id', $tank_ids);
    }
}