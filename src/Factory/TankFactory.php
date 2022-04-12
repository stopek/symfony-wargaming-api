<?php

namespace App\Factory;

use App\Collection\EntityCollection;
use App\Entity\Tank;
use App\Repository\TankRepository;

class TankFactory
{
    public function __construct(private TankRepository $tankRepository)
    {
    }

    /**
     * @param array $tanks
     * @param Tank[] $existing_tanks
     */
    public function createOrUpdate(array $tanks, array $existing_tanks = [])
    {
        $collection = new EntityCollection();

        foreach ($tanks as $tank) {
            $tank_id = $tank['tank_id'];

            $tankEntity = new Tank();
            $tankEntity->setId($tank_id);

            if (isset($existing_tanks[$tank_id])) {
                $tankEntity = $existing_tanks[$tank_id];
            }

            $tankEntity
                ->setDescription($tank['description'])
                ->setShortName($tank['short_name'])
                ->setPriceGold($tank['price_gold'] ?? 0)
                ->setPriceCredit($tank['price_credit'] ?? 0)
                ->setNation($tank['nation'])
                ->setIsPremium($tank['is_premium'] ?? false)
                ->setImage($tank['images']['big_icon'] ?? null)
                ->setPricesXp($tank['prices_xp'] ?? 0)
                ->setTier($tank['tier'])
                ->setType($tank['type'])
                ->setTag($tank['tag'])
                ->setName($tank['name']);

            $collection->offsetSet('', $tankEntity);
        }

        $this->tankRepository->saveMultiple($collection);
    }

    public function updateStatistics(array $tank, array $existing_tanks = [])
    {
    }
}