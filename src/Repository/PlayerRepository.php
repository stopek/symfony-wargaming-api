<?php

namespace App\Repository;

use App\Entity\Clan;
use App\Entity\Player;
use App\Helpers\ArrayHelper;
use App\Trait\Repository\StoreTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    use StoreTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * @param int $limit
     * @param DateTimeInterface $date
     * @return Player[]
     */
    public function getPlayersToSaveHistory(int $limit, DateTimeInterface $date)
    {
        $query = $this->createQueryBuilder('p');

        $query
            ->leftJoin('p.playerStatsHistories', 'history', Join::WITH, 'history.created_at = :update_date')
            ->where('history IS NULL')
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->orX(
                        $query->expr()->eq('p.is_locked_history', ':locked_false'),
                        $query->expr()->isNull('p.is_locked_history')
                    ),
                    $query->expr()->andX(
                        $query->expr()->eq('p.is_locked_history', ':locked_true'),
                        $query->expr()->lt('p.locked_history_at', ':date_locked')
                    )
                )
            )
            ->andWhere('p.deleted_at IS NULL')
            ->andWhere($query->expr()->orX(
                $query->expr()->isNull('p.is_inactive'),
                $query->expr()->eq('p.is_inactive', ':is_inactive')
            ))
            ->orderBy('p.updates', 'DESC')
            ->addOrderBy('p.last_battle_time', 'ASC')
            ->setMaxResults($limit)
            ->setParameters([
                'update_date' => $date->format('Y-m-d'),
                'locked_false' => false,
                'is_inactive' => false,
                'locked_true' => true,
                'date_locked' => (new DateTime())->modify('-30 minutes')
            ]);

        return $query
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $inactive_date_modifiers
     * @return mixed
     */
    public function setPlayersInactive(string $inactive_date_modifiers = '-2 months'): mixed
    {
        $query = $this->getActivePlayersQuery();

        $query
            ->andWhere('FROM_UNIXTIME(p.last_battle_time) < :inactive_date')
            ->andWhere('p.inactive_at IS NULL')
            ->update()
            ->set('p.is_inactive', ':is_inactive')
            ->set('p.inactive_at', ':inactive_at')
            ->setParameter('inactive_date', (new DateTime())->modify($inactive_date_modifiers))
            ->setParameter('is_inactive', true)
            ->setParameter('inactive_at', new DateTime());

        return $query->getQuery()->execute();
    }

    private function getActivePlayersQuery(): QueryBuilder
    {
        $query = $this->createQueryBuilder('p');

        return
            $query
                ->where('p.deleted_at IS NULL')
                ->andWhere('p.is_inactive IS NULL or p.is_inactive = :inactive')
                ->setParameter('inactive', false);
    }

    /**
     * @param int $limit
     * @return Player[]
     */
    public function getPlayersToCheckActivity(int $limit = 100): array
    {
        $query = $this->createQueryBuilder('p');

        $data = $query
            ->where('p.deleted_at IS NULL')
            ->andWhere('p.is_inactive = :inactive')
            ->setParameter('inactive', true)
            ->orderBy('p.inactive_at', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return ArrayHelper::arrayWithKeyFromObjectCollection($data, function (Player $player) {
            return $player->getId();
        });
    }

    public function countLockedPlayers(bool $only_online = false): array
    {
        $query = $this->playersToUpdateQuery();

        $query
            ->andWhere(
                $query->expr()->andX(
                    $query->expr()->eq('p.is_locked', ':locked_true'),
                    $query->expr()->gt('p.locked_at', ':date_locked')
                )
            )
            ->setParameter('date_locked', (new DateTime())->modify('-30 minutes'))
            ->setParameter('locked_true', true);

        if (true === $only_online) {
            $query
                ->andWhere('p.last_online_at >= :max_online_date')
                ->setParameter('max_online_date', (new DateTime())->modify('-30 minutes'));
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    private function playersToUpdateQuery(): QueryBuilder
    {
        return $this
            ->getActivePlayersQuery()
            ->orderBy('p.updates', 'ASC')
            ->addOrderBy('p.last_battle_time', 'DESC');
    }

    /**
     * @return Player[]
     */
    public function getPlayerToUpdate(bool $only_online = false, int $limit = 100): array
    {
        $query = $this->playersToUpdateQuery();

        $query = $query
            ->setMaxResults($limit)
            ->andWhere(
                $query->expr()->orX(
                    $query->expr()->orX(
                        $query->expr()->eq('p.is_locked', ':locked_false'),
                        $query->expr()->isNull('p.is_locked')
                    ),
                    $query->expr()->andX(
                        $query->expr()->eq('p.is_locked', ':locked_true'),
                        $query->expr()->lt('p.locked_history_at', ':date_locked')
                    )
                )
            )

//            ->andWhere('p.id = :id')->setParameter('id', 1077615947)
            ->setParameter('locked_false', false)
            ->setParameter('date_locked', (new DateTime())->modify('-30 minutes'))
            ->setParameter('locked_true', true);

        if ($only_online) {
            $query
                ->andWhere('p.last_online_at >= :max_online_date')
                ->setParameter('max_online_date', (new DateTime())->modify('-30 minutes'));
        }

        /** @var Player[] $players */
        $players = $query->getQuery()->getResult();

        $output = [];
        foreach ($players as $player) {
            $output[$player->getId()] = $player;
        }

        return $output;
    }

    /**
     * @param array $role_players
     * @return bool
     */
    public function updatePlayersForRole(array $role_players = []): bool
    {
        foreach ($role_players as $role => $players) {
            $query = $this->createQueryBuilder('p');

            $query
                ->where('p.id IN(:ids)')->setParameter('ids', $players)
                ->update()
                ->set('p.role', ':role')
                ->setParameter('role', $role);

            $query->getQuery()->execute();
        }

        return true;
    }

    /**
     * @param Player[] $players
     */
    public function lockPlayers(array $players)
    {
        foreach ($players as $player) {
            $player->setLockedAt(new DateTime());
            $player->setIsLocked(true);
            $this->_em->persist($player);
        }

        $this->_em->flush();
    }

    /**
     * @param Player[] $players
     */
    public function lockHistoryPlayers(array $players, bool $type)
    {
        foreach ($players as $player) {
            $player->setLockedHistoryAt($type ? new DateTime() : null);
            $player->setIsLockedHistory($type);

            $this->_em->persist($player);
        }

        $this->_em->flush();
    }

    /**
     * @param int $player_id
     * @return Player|null
     */
    public function getPlayerById(int $player_id): ?Player
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.id = :player_id')
            ->setParameter('player_id', $player_id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $player_name
     * @return QueryBuilder
     */
    public function getPlayersByNameQuery(string $player_name): QueryBuilder
    {
        $q = $this->createQueryBuilder('p');

        return $q
            ->where(
                $q->expr()->like('p.name', ':player_name')
            )
            ->orderBy('p.is_inactive', 'ASC')
            ->addOrderBy('p.last_battle_time', 'DESC')
            ->setParameter('player_name', '%' . $player_name . '%');
    }

    /**
     * @return array
     */
    public function getAllPlayersIds(): array
    {
        $data = $this
            ->createQueryBuilder('p')
            ->select(['p.id'])
            ->getQuery()
            ->getResult();

        return ArrayHelper::arrayWithKeyFromMultidimensional($data, 'id');
    }

    public function getAllPlayersForSitemap(): array
    {
        return $this
            ->getActivePlayersQuery()
            ->select(['p.id', 'p.updated_at', 'p.name'])
            ->getQuery()
            ->getResult();
    }

    public function getPlayerIdsFromClan(Clan $clan): array
    {
        $players = $this
            ->getPlayersIdsQuery()
            ->select(['p.id', 'stats.battles'])
            ->join('p.stats', 'stats')
            ->andWhere('p.clan = :clan')
            ->setParameter('clan', $clan)
            ->getQuery()
            ->getResult();

        return ArrayHelper::arrayWithKeyFromMultidimensional($players, 'id');
    }

    private function getPlayersIdsQuery(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.deleted_at IS NULL')
            ->select(['p.id']);
    }

    public function getPlayerIdsFromClanCollection(Clan $clan): array
    {
        $players = $this
            ->createQueryBuilder('p')
            ->where('p.deleted_at IS NULL')
            ->join('p.stats', 'stats')
            ->andWhere('p.clan = :clan')
            ->setParameter('clan', $clan)
            ->getQuery()
            ->getResult();

        return ArrayHelper::arrayWithKeyFromObjectCollection($players, function (Player $player) {
            return $player->getId();
        });
    }

    /**
     * @param array $players_ids
     * @return int|mixed|string
     */
    public function unpinPlayersFromClan(array $players_ids = []): mixed
    {
        $query = $this->createQueryBuilder('p');

        $query
            ->where('p.id IN(:ids)')->setParameter('ids', $players_ids)
            ->update()
            ->set('p.clan', ':null')
            ->set('p.role', ':null')
            ->setParameter('null', null);

        return $query->getQuery()->execute();
    }

    /**
     * @return Player[]
     */
    public function getActivePlayers(): array
    {
        $players = $this
            ->getActivePlayersQuery()
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();

        return ArrayHelper::arrayWithKeyFromObjectCollection($players, function (Player $player) {
            return $player->getId();
        });
    }

    /**
     * Aktualizujemy ostatnią datę online zalogowanego gracza.
     * UWAGA. Nie możemy tutaj sprawdzać czy gracz nie jest właśnie aktualizowany
     * ponieważ inny proces może zblokować gracza.
     * Data aktualizacji musi być zawsze zaktualizowana.
     *
     * @param Player $player
     */
    public function updateOnlineDate(Player $player)
    {
        $player->setLastOnlineAt(new DateTime());
        $player->setOnlineTime(intval($player->getOnlineTime()) + 30);

        $this->save($player);
    }
}
