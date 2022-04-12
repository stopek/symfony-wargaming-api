<?php

namespace App\Repository;

use App\Entity\ExpTanks;
use App\Helpers\ArrayHelper;
use App\Interface\SaveRepositoryInterface;
use App\Trait\Repository\StoreTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method ExpTanks|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpTanks|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpTanks[]    findAll()
 * @method ExpTanks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpTanksRepository extends ServiceEntityRepository implements SaveRepositoryInterface
{
    use StoreTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpTanks::class);
    }

    /**
     * @return ExpTanks[]
     */
    public function loadAllTanks(): array
    {
        /** @var ExpTanks[] $tanks */
        $tanks = $this
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        $output = [];
        foreach ($tanks as $tank) {
            $output[$tank->getTag()] = $tank;
        }

        return $output;
    }

    public function getLastTankStatsWithTanks(int $update_id, Request $request): array
    {
        $query = $this
            ->createQueryBuilder('t')
            ->join('t.update_owner', 'update_owner')
            ->andWhere('update_owner.id = :owner_id')->setParameter('owner_id', $update_id)
            ->orderBy('update_owner.version', 'desc')
            ->join('t.tank', 'tank');

        if ($request->get('tank_name')) {
            $query
                ->andWhere('tank.name LIKE :tank_name')
                ->setParameter('tank_name', '%' . $request->get('tank_name') . '%');
        }

        return $query->getQuery()->getResult();
    }

    public function getLastTankStats(): array
    {
        $tanks = $this
            ->tanksStatsQuery()
            ->getQuery()
            ->getResult();

        return ArrayHelper::arrayWithKeyFromMultidimensional($tanks, 'tag');
    }

    public function tanksStatsQuery(): QueryBuilder
    {
        $columns = [
            't.damage', 't.def', 't.frag', 't.win', 't.spot', 't.tag', 't.tier'
        ];

        return $this
            ->createQueryBuilder('t')
            ->select($columns)
            ->join('t.update_owner', 'update_owner')
            ->orderBy('update_owner.version', 'desc');
    }

    /**
     * @param string $tank_tag
     * @return array
     */
    public function getTankStatByTag(string $tank_tag): array
    {
        return $this
            ->tanksStatsQuery()
            ->where('t.tag = :tag')
            ->setParameter('tag', $tank_tag)
            ->getQuery()
            ->getResult();
    }
}
