<?php

namespace App\Repository;

use App\Entity\Clan;
use App\Helpers\ArrayHelper;
use App\Interface\SaveRepositoryInterface;
use App\Trait\Repository\StoreTrait;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Clan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clan[]    findAll()
 * @method Clan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClanRepository extends ServiceEntityRepository implements SaveRepositoryInterface
{
    use StoreTrait;

    /**
     * ClanRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clan::class);
    }

    public function getAllActiveClansTags(): array
    {
        $data = $this
            ->getActiveClansQuery()
            ->select('c.tag')
            ->getQuery()
            ->getResult();

        return ArrayHelper::getFromMultidimensional('tag', $data);
    }

    private function getActiveClansQuery(): QueryBuilder
    {
        $query = $this->createQueryBuilder('c');

        return $query
            ->where($query->expr()->isNull('c.deleted_at'))
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->isNull('c.is_disbanded'),
                    $query->expr()->eq('c.is_disbanded', ':disbanded')
                )
            )
            ->setParameter('disbanded', false);
    }

    /**
     * @param int $page
     * @param int $limit
     */
    public function getClansList(Request $request, int $page, int $perPage): array
    {
        $page = max(0, $page - 1);
        $query = $this->clansQuery($request);

        $data = $query
            ->join('c.players', 'player', Join::WITH, 'c.id = player.clan')
            ->join('player.playerStatsHistories', 'history', Join::WITH, 'player.id = history.player')
            ->select([
                'c',
                'AVG(history.wn8) as wn8',
                'COUNT(DISTINCT player.id) as active_players'
            ])
            ->orderBy('wn8', 'DESC')
            ->andWhere($query->expr()->orX(
                $query->expr()->isNull('player.is_inactive'),
                $query->expr()->eq('player.is_inactive', ':is_inactive')
            ))
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->isNull('c.is_disbanded'),
                    $query->expr()->eq('c.is_disbanded', ':disbanded')
                )
            )
            ->andWhere('history.created_at > :min_history')->setParameter('min_history', (new DateTime())->modify('-7 days'))
            ->having('active_players >= :min_active')
            ->setParameter('min_active', 3)
            ->setParameter('disbanded', 0)
            ->groupBy('c.id')
            ->setParameter('is_inactive', false)
            ->setFirstResult($page * $perPage)
            ->setMaxResults($perPage);

        $output = array_map(function ($item) {
            /** @var Clan $clan */
            $clan = $item[0];
            $clan->setWn8($item['wn8'] ?? -1);
            $clan->setActivePlayers($item['active_players'] ?? 0);

            return $clan;
        }, $data->getQuery()->getResult());

        return $output;
    }

    private function clansQuery(Request $request): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('c')
            ->andWhere('c.deleted_at IS NULL');

        $name = $request->get('clan_name');
        if (strlen($name) > 0) {
            $query
                ->andWhere(
                    $query->expr()->orX(
                        $query->expr()->like('c.tag', ':name'),
                        $query->expr()->like('c.name', ':name')
                    )
                )
                ->setParameter('name', '%' . $name . '%');
        }

        $amount = $request->get('amount');
        if (is_array($amount)) {
            $query
                ->andWhere('c.members_count >= :from')
                ->andWhere('c.members_count <= :to')
                ->setParameter('from', $amount[0])
                ->setParameter('to', $amount[1]);
        }

        return $query;
    }

    /**
     * @param Request $request
     * @return int
     */
    public function getClansTotalItems(Request $request): int
    {
        $query = $this->clansQuery($request);

        $list = $query
            ->leftJoin('c.players', 'player')
            ->select(['count(player.id) as active_players'])
            ->andWhere($query->expr()->orX(
                $query->expr()->isNull('player.is_inactive'),
                $query->expr()->eq('player.is_inactive', ':is_inactive')
            ))
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->isNull('c.is_disbanded'),
                    $query->expr()->eq('c.is_disbanded', ':disbanded')
                )
            )
            ->groupBy('c.id')
            ->having('count(player.id) >= :min_active')
            ->setParameter('min_active', 3)
            ->setParameter('disbanded', 0)
            ->setParameter('is_inactive', false)
            ->getQuery()->getResult();

        return count($list);
    }

    /**
     * @param ?string $tag_or_id
     * @return ?Clan
     */
    public function getClanByTagOrdId(?string $tag_or_id)
    {
        $query = $this->createQueryBuilder('clan');

        return $query
            ->where(
                $query->expr()->orX(
                    $query->expr()->eq('clan.id', ':tag_or_id'),
                    $query->expr()->eq('clan.tag', ':tag_or_id')
                )
            )
            ->andWhere('clan.deleted_at IS NULL')
            ->andWhere('clan.is_disbanded IS NULL OR clan.is_disbanded = :disbanded')
            ->setParameter('disbanded', 0)
            ->setParameter('tag_or_id', $tag_or_id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $limit
     * @return Clan[]
     */
    public function getClansToUpdateWithIdKeys(int $limit = 100): array
    {
        /** @var Clan[] $clans */
        $clans = $this
            ->createQueryBuilder('c')
            ->orderBy('c.updated_at', 'ASC')
            ->where('c.deleted_at IS NULL')
//            ->andWhere('c.id = :id')->setParameter('id', 1073751696)
            ->orderBy('c.updates', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return ArrayHelper::arrayWithKeyFromObjectCollection($clans, function (Clan $clan) {
            return $clan->getId();
        });
    }

    /**
     * @return Clan[]
     */
    public function getClanIds(): array
    {
        return $this
            ->createQueryBuilder('c')
            ->select(['c.id'])
            ->getQuery()
            ->getResult();
    }
}
