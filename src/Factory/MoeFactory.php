<?php

namespace App\Factory;

use App\Collection\EntityCollection;
use App\Entity\MoeTanks;
use App\Entity\MoeUpdate;
use App\Repository\MoeTanksRepository;
use App\Repository\MoeUpdateRepository;
use App\Repository\TankRepository;
use DateTime;

class MoeFactory
{
    public function __construct(
        private MoeTanksRepository  $moeTanksRepository,
        private TankRepository      $tankRepository,
        private MoeUpdateRepository $moeUpdateRepository
    )
    {
    }

    public function createUpdate(array $data): MoeUpdate
    {
        $update = new MoeUpdate();
        $update->setVersion(DateTime::createFromFormat('Y-m-d\TH:i:s', $data['Date'])->format('Y-m-d'));

        $this->moeUpdateRepository->save($update);

        return $update;
    }

    public function create(array $moeList, MoeUpdate $expUpdate)
    {
        $collection = new EntityCollection();

        foreach ($moeList as $moe) {
            $tankItem = $this->tankRepository->find($moe['TankId']);

            $expTank = new MoeTanks();
            $expTank
                ->setTank($tankItem)
                ->setBattles($moe['NumberOfBattles'])
                ->setMoe1dmg($moe['Moe1Dmg'])
                ->setMoe2dmg($moe['Moe2Dmg'])
                ->setMoe3dmg($moe['Moe3Dmg'])
                ->setUpdateOwner($expUpdate);

            $collection->offsetSet('', $expTank);
        }

        $this->moeTanksRepository->saveMultiple($collection);
    }
}