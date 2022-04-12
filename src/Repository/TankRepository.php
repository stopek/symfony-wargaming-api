<?php

namespace App\Repository;

use App\Entity\Tank;
use App\Helpers\ArrayHelper;
use App\Trait\Repository\StoreTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Tank|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tank|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tank[]    findAll()
 * @method Tank[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TankRepository extends ServiceEntityRepository
{
    use StoreTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tank::class);
    }

    public function getAllTanksIds(): array
    {
        $data = $this
            ->createQueryBuilder('t')
            ->select('t.id')
            ->getQuery()
            ->getResult();

        return ArrayHelper::getFromMultidimensional('id', $data);
    }

    /**
     * @return Tank[]
     */
    public function getTanksWithIdKey(): array
    {
        $tanks = $this->getAllTanks();

        $output = [];
        foreach ($tanks as $tank) {
            $output[$tank->getId()] = $tank;
        }

        return $output;
    }

    /**
     * @return Tank[]
     */
    public function getAllTanks(): array
    {
        return $this
            ->getAllTanksQuery()
            ->getQuery()
            ->getResult();
    }

    public function getAllTanksQuery(?Request $request = null): QueryBuilder
    {
        $query = $this->createQueryBuilder('t');

        if (null === $request) {
            return $query;
        }

        $tier = $request->get('tier');
        if (!empty($tier)) {
            $query->andWhere('t.tier = :tier')->setParameter('tier', $tier);
        }

        $nation = $request->get('nation');
        if (!empty($nation)) {
            $query->andWhere('t.nation = :nation')->setParameter('nation', $nation);
        }

        $type = $request->get('type');
        if (!empty($type)) {
            $query->andWhere('t.type = :type')->setParameter('type', $type);
        }

        $premium = $request->get('premium');
        if ('only_premium' === $premium) {
            $query->andWhere('t.is_premium = :premium')->setParameter('premium', true);
        }

        if ('without_premium' === $premium) {
            $query->andWhere('t.is_premium = :premium')->setParameter('premium', false);
        }

        $tank_name = $request->get('tank_name');
        if ($tank_name) {
            $query
                ->andWhere(
                    $query->expr()->orX(
                        $query->expr()->like('t.tag', ':tank_name'),
                        $query->expr()->like('t.name', ':tank_name'),
                        $query->expr()->like('t.short_name', ':tank_name'),
                        $query->expr()->like('t.description', ':tank_name')
                    )
                )
                ->setParameter('tank_name', '%' . $tank_name . '%');
        }
        return $query;
    }

    /**
     * @param array $ids
     * @return Tank[]
     */
    public function getTanksByIdsWithTagKey(array $ids): array
    {
        $data = $this
            ->getAllTanksQuery()
            ->andWhere('t.id IN(:ids)')->setParameter('ids', array_values($ids))
            ->getQuery()
            ->getResult();

        return ArrayHelper::arrayWithKeyFromObjectCollection($data, function (Tank $tank) {
            return $tank->getTag();
        }, 'tank');
    }

    /**
     * @param string $tag
     * @return ?Tank
     */
    public function findTankByTag(string $tag): ?Tank
    {
        return $this
            ->createQueryBuilder('t')
            ->where('t.tag = :tag')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
