<?php

namespace App\Repository;

use App\Entity\TankCrew;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TankCrew|null find($id, $lockMode = null, $lockVersion = null)
 * @method TankCrew|null findOneBy(array $criteria, array $orderBy = null)
 * @method TankCrew[]    findAll()
 * @method TankCrew[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TankCrewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TankCrew::class);
    }
}
