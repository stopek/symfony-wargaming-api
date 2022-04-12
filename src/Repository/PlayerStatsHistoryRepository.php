<?php

namespace App\Repository;

use App\Entity\PlayerStatsHistory;
use App\Trait\Repository\StoreTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerStatsHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerStatsHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerStatsHistory[]    findAll()
 * @method PlayerStatsHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerStatsHistoryRepository extends ServiceEntityRepository
{
    use StoreTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerStatsHistory::class);
    }

    public function store(PlayerStatsHistory $history)
    {
        $this->_em->persist($history);
        $this->_em->flush();
    }
}
