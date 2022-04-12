<?php

namespace App\Wargaming\Api;

use App\Wargaming\ApiResponse;

class Clan
{
    use ApiTrait;

    /**
     * Szuka klanu według nazwy klanu.
     *
     * @param string $name
     * @param array $fields
     * @return ApiResponse
     */
    public function searchClan(string $name, array $fields = []): ApiResponse
    {
        return $this->api->get('clans/list', [
            'search' => $name,
            'fields' => $fields
        ]);
    }

    /**
     * Pobiera szczegóły klanu dla konkretnego id klanu.
     *
     * @param int $clan_id
     * @return ApiResponse
     */
    public function clanDetails(int $clan_id): ApiResponse
    {
        return $this->getClanDetails($clan_id);
    }

    /**
     * Pobiera szczegóły klanu dla przekazanego id lub tablicy identyfikatorów klanu.
     *
     * @param int|array $clan_id_or_ids
     * @return ApiResponse
     */
    private function getClanDetails(int|array $clan_id_or_ids, array $extra = []): ApiResponse
    {
        return $this->api->get('clans/info', [
            'clan_id' => $clan_id_or_ids,
            'extra' => $extra
        ]);
    }

    /**
     * Pobiera szczegóły klanów dla przekazany identyfikatorów.
     *
     * @param array $clans_ids
     * @param array $extra
     * @return ApiResponse
     */
    public function clansDetails(array $clans_ids, array $extra = []): ApiResponse
    {
        return $this->getClanDetails($clans_ids, $extra);
    }

    /**
     * Pobiera szczegóły przynależności gracza do klanu
     *
     * @param int $account_id
     * @return ApiResponse
     */
    public function clanPlayerData(int $account_id): ApiResponse
    {
        return $this->api->get('clans/accountinfo', [
            'account_id' => $account_id
        ]);
    }

    /**
     * @param array $account_ids
     * @param array $fields
     * @return ApiResponse
     */
    public function clanPlayersData(array $account_ids, array $fields = []): ApiResponse
    {
        return $this->api->get('clans/accountinfo', [
            'account_id' => $account_ids,
            'fields' => $fields
        ]);
    }

    /**
     * @return ApiResponse
     */
    public function clanGlossary(): ApiResponse
    {
        return $this->api->get('clans/glossary');
    }

    /**
     * @param int $page
     * @param array $fields
     * @return ApiResponse
     */
    public function clans(int $page = 1, array $fields = []): ApiResponse
    {
        return $this->api->get('clans/list', [
            'page_no' => max(1, $page),
            'fields' => $fields
        ]);
    }
}