<?php

namespace App\Factory;

use App\Collection\EntityCollection;
use App\Entity\ExpTanks;
use App\Entity\ExpUpdate;
use App\Repository\ExpTanksRepository;
use App\Repository\TankRepository;

class ExpTanksFactory
{
    public function __construct(
        private ExpTanksRepository $expTanksRepository,
        private TankRepository     $tankRepository
    )
    {
    }

    public function create(array $tanks, ExpUpdate $expUpdate)
    {
        $collection = new EntityCollection();

        foreach ($tanks as $expectedTank) {
            $tank = $this->tankRepository->findTankByTag($expectedTank['Tag']);

            $expTank = new ExpTanks();
            $expTank
                ->setTank($tank)
                ->setDamage($expectedTank['Damage'])
                ->setDef($expectedTank['Def'])
                ->setFrag($expectedTank['Frag'])
                ->setName($expectedTank['FullName'])
                ->setNation($expectedTank['NatioName'])
                ->setSpot($expectedTank['Spot'])
                ->setTier($expectedTank['Tier'])
                ->setWin($expectedTank['WinRate'])
                ->setType($expectedTank['Type'])
                ->setTag($expectedTank['Tag'])
                ->setImage($expectedTank['SmallImageUrl'])
                ->setUpdateOwner($expUpdate);

            $collection->offsetSet('', $expTank);
        }

        $this->expTanksRepository->saveMultiple($collection);
    }
}