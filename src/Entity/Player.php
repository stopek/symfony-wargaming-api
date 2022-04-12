<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Player extends EntityBase
{
    /**
     * @Groups({"api_data", "auth_user", "tank_with_tank_exp", "g_players_list"})
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"api_data", "auth_user", "tank_with_tank_exp", "player_details", "g_players_list"})
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @Groups({"api_data", "auth_user", "g_players_list"})
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $player_created_at;

    /**
     * @Groups({"auth_user", "api_data", "player_details"})
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $player_updated_at;

    /**
     * @Groups({"auth_user", "api_data", "player_details", "g_players_list"})
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $last_battle_time;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $global_rating;

    /**
     * @Groups({"api_data", "player_details", "g_players_list"})
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $role;

    /**
     * @Groups({"api_data", "player_details"})
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $player_joined_at;

    /**
     * @Groups({"api_data", "player_details", "g_players_list_base_stats"})
     * @ORM\OneToMany(targetEntity=Stats::class, mappedBy="player", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $stats;

    /**
     * @Groups({"auth_user"})
     * @ORM\Column(type="integer")
     */
    private $updates;

    /**
     * @Groups({"player_details"})
     * @ORM\OneToMany(targetEntity=TanksStats::class, mappedBy="player", fetch="EXTRA_LAZY")
     */
    private $tanksStats;

    /**
     * @Groups({"auth_user", "player_details", "g_player_clan_base_info", "tank_with_tank_exp"})
     * @ORM\ManyToOne(targetEntity=Clan::class, inversedBy="players")
     */
    private $clan;

    /**
     * @Groups({"auth_user"})
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="player")
     * @OrderBy({"deleted_at" = "ASC"})
     */
    private $users;

    /** @ORM\Column(type="datetime", nullable=true) */
    private $deleted_at;

    /**
     * @Groups({"auth_user", "g_player_stats_history"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_locked;

    /**
     * @ORM\OneToMany(targetEntity=Clan::class, mappedBy="leader")
     */
    private $leader_clans;

    /**
     * @ORM\OneToMany(targetEntity=Clan::class, mappedBy="creator")
     */
    private $creator_clans;

    private float|int $wn8;

    private float|int $weight;

    /**
     * @Groups({"g_player_stats_history"})
     * @ORM\OneToMany(targetEntity=PlayerStatsHistory::class, mappedBy="player", orphanRemoval=true)
     */
    private $playerStatsHistories;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_locked_history;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $locked_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $locked_history_at;

    /**
     * @Groups({"api_data", "g_players_list"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_inactive;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $inactive_at;

    /**
     * @ORM\OneToMany(targetEntity=Search::class, mappedBy="player")
     */
    private $searches;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_online_at;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="organizer")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity=GamePlayer::class, mappedBy="player")
     */
    private $gamePlayers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $online_time;

    public function __construct()
    {
        $this->stats = new ArrayCollection();
        $this->tanksStats = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->leader_clans = new ArrayCollection();
        $this->creator_clans = new ArrayCollection();
        $this->playerStatsHistories = new ArrayCollection();
        $this->searches = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->gamePlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getClan(): ?Clan
    {
        return $this->clan;
    }

    public function setClan(?Clan $clan): self
    {
        $this->clan = $clan;

        return $this;
    }

    public function getPlayerCreatedAt(): ?string
    {
        return $this->player_created_at;
    }

    public function setPlayerCreatedAt(?string $player_created_at): self
    {
        $this->player_created_at = $player_created_at;

        return $this;
    }

    public function canUpdatePlayer($updated_date_unix): bool
    {
        return (string)$this->getPlayerUpdatedAt() !== (string)$updated_date_unix;
    }

    public function getPlayerUpdatedAt(): ?string
    {
        return $this->player_updated_at;
    }

    public function setPlayerUpdatedAt(?string $player_updated_at): self
    {
        $this->player_updated_at = $player_updated_at;

        return $this;
    }

    public function canUpdateLastBattlePlayer($last_battle_date_unix): bool
    {
        return (string)$this->getLastBattleTime() !== (string)$last_battle_date_unix;
    }

    public function getLastBattleTime(): ?string
    {
        return $this->last_battle_time;
    }

    public function setLastBattleTime(?string $last_battle_time): self
    {
        $this->last_battle_time = $last_battle_time;

        return $this;
    }

    public function getGlobalRating(): ?int
    {
        return $this->global_rating;
    }

    public function setGlobalRating(?int $global_rating): self
    {
        $this->global_rating = $global_rating;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPlayerJoinedAt(): ?string
    {
        return $this->player_joined_at;
    }

    public function setPlayerJoinedAt(?string $player_joined_at): self
    {
        $this->player_joined_at = $player_joined_at;

        return $this;
    }

    /**
     * @return Collection|Stats[]
     */
    public function getStats(): Collection
    {
        return $this->stats;
    }

    public function addStat(Stats $stat): self
    {
        if (!$this->stats->contains($stat)) {
            $this->stats[] = $stat;
            $stat->setPlayer($this);
        }

        return $this;
    }

    public function removeStat(Stats $stat): self
    {
        if ($this->stats->removeElement($stat)) {
            // set the owning side to null (unless already changed)
            if ($stat->getPlayer() === $this) {
                $stat->setPlayer(null);
            }
        }

        return $this;
    }

    public function getUpdates(): ?int
    {
        return $this->updates;
    }

    public function setUpdates(int $updates): self
    {
        $this->updates = $updates;

        return $this;
    }

    /**
     * @return Collection|TanksStats[]
     */
    public function getTanksStats(): Collection
    {
        return $this->tanksStats;
    }

    public function getTankStats(Tank $tank)
    {
        return $this->tanksStats->filter(function (TanksStats $t) use ($tank) {
            return $t->getTank()->getId() === $tank->getId();
        });
    }

    public function addTanksStat(TanksStats $tanksStat): self
    {
        if (!$this->tanksStats->contains($tanksStat)) {
            $this->tanksStats[] = $tanksStat;
            $tanksStat->setPlayer($this);
        }

        return $this;
    }

    public function removeTanksStat(TanksStats $tanksStat): self
    {
        if ($this->tanksStats->removeElement($tanksStat)) {
            // set the owning side to null (unless already changed)
            if ($tanksStat->getPlayer() === $this) {
                $tanksStat->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setPlayer($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getPlayer() === $this) {
                $user->setPlayer(null);
            }
        }

        return $this;
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getIsLocked(): ?bool
    {
        return $this->is_locked;
    }

    public function setIsLocked(?bool $is_locked): self
    {
        $this->is_locked = $is_locked;

        return $this;
    }

    /**
     * @return Collection|Clan[]
     */
    public function getLeaderClans(): Collection
    {
        return $this->leader_clans;
    }

    public function addLeaderClan(Clan $leaderClan): self
    {
        if (!$this->leader_clans->contains($leaderClan)) {
            $this->leader_clans[] = $leaderClan;
            $leaderClan->setLeader($this);
        }

        return $this;
    }

    public function removeLeaderClan(Clan $leaderClan): self
    {
        if ($this->leader_clans->removeElement($leaderClan)) {
            // set the owning side to null (unless already changed)
            if ($leaderClan->getLeader() === $this) {
                $leaderClan->setLeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Clan[]
     */
    public function getCreatorClans(): Collection
    {
        return $this->creator_clans;
    }

    public function addCreatorClan(Clan $creatorClan): self
    {
        if (!$this->creator_clans->contains($creatorClan)) {
            $this->creator_clans[] = $creatorClan;
            $creatorClan->setCreator($this);
        }

        return $this;
    }

    public function removeCreatorClan(Clan $creatorClan): self
    {
        if ($this->creator_clans->removeElement($creatorClan)) {
            // set the owning side to null (unless already changed)
            if ($creatorClan->getCreator() === $this) {
                $creatorClan->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return float|int
     */
    public function getWn8(): float|int
    {
        return $this->wn8;
    }

    /**
     * @param float|int $wn8
     */
    public function setWn8(float|int $wn8): void
    {
        $this->wn8 = $wn8;
    }

    /**
     * @return float|int
     */
    public function getWeight(): float|int
    {
        return $this->weight;
    }

    /**
     * @param float|int $weight
     */
    public function setWeight(float|int $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return Collection|PlayerStatsHistory[]
     */
    public function getPlayerStatsHistories(): Collection
    {
        return $this->playerStatsHistories;
    }

    public function addPlayerStatsHistory(PlayerStatsHistory $playerStatsHistory): self
    {
        if (!$this->playerStatsHistories->contains($playerStatsHistory)) {
            $this->playerStatsHistories[] = $playerStatsHistory;
            $playerStatsHistory->setPlayer($this);
        }

        return $this;
    }

    public function removePlayerStatsHistory(PlayerStatsHistory $playerStatsHistory): self
    {
        if ($this->playerStatsHistories->removeElement($playerStatsHistory)) {
            // set the owning side to null (unless already changed)
            if ($playerStatsHistory->getPlayer() === $this) {
                $playerStatsHistory->setPlayer(null);
            }
        }

        return $this;
    }

    public function getIsLockedHistory(): ?bool
    {
        return $this->is_locked_history;
    }

    public function setIsLockedHistory(?bool $is_locked_history): self
    {
        $this->is_locked_history = $is_locked_history;

        return $this;
    }

    public function getLockedAt(): ?DateTimeInterface
    {
        return $this->locked_at;
    }

    public function setLockedAt(?DateTimeInterface $locked_at): self
    {
        $this->locked_at = $locked_at;

        return $this;
    }

    public function getLockedHistoryAt(): ?DateTimeInterface
    {
        return $this->locked_history_at;
    }

    public function setLockedHistoryAt(?DateTimeInterface $locked_history_at): self
    {
        $this->locked_history_at = $locked_history_at;

        return $this;
    }

    public function getIsInactive(): ?bool
    {
        return $this->is_inactive;
    }

    public function setIsInactive(?bool $is_inactive): self
    {
        $this->is_inactive = $is_inactive;

        return $this;
    }

    public function getInactiveAt(): ?DateTimeInterface
    {
        return $this->inactive_at;
    }

    public function setInactiveAt(?DateTimeInterface $inactive_at): self
    {
        $this->inactive_at = $inactive_at;

        return $this;
    }

    /**
     * @return Collection|Search[]
     */
    public function getSearches(): Collection
    {
        return $this->searches;
    }

    public function addSearch(Search $search): self
    {
        if (!$this->searches->contains($search)) {
            $this->searches[] = $search;
            $search->setPlayer($this);
        }

        return $this;
    }

    public function removeSearch(Search $search): self
    {
        if ($this->searches->removeElement($search)) {
            // set the owning side to null (unless already changed)
            if ($search->getPlayer() === $this) {
                $search->setPlayer(null);
            }
        }

        return $this;
    }

    public function getLastOnlineAt(): ?DateTimeInterface
    {
        return $this->last_online_at;
    }

    public function setLastOnlineAt(?DateTimeInterface $last_online_at): self
    {
        $this->last_online_at = $last_online_at;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setOrganizer($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getOrganizer() === $this) {
                $game->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GamePlayer[]
     */
    public function getGamePlayers(): Collection
    {
        return $this->gamePlayers;
    }

    public function addGamePlayer(GamePlayer $gamePlayer): self
    {
        if (!$this->gamePlayers->contains($gamePlayer)) {
            $this->gamePlayers[] = $gamePlayer;
            $gamePlayer->setPlayer($this);
        }

        return $this;
    }

    public function removeGamePlayer(GamePlayer $gamePlayer): self
    {
        if ($this->gamePlayers->removeElement($gamePlayer)) {
            // set the owning side to null (unless already changed)
            if ($gamePlayer->getPlayer() === $this) {
                $gamePlayer->setPlayer(null);
            }
        }

        return $this;
    }

    public function getOnlineTime(): ?int
    {
        return $this->online_time;
    }

    public function setOnlineTime(?int $online_time): self
    {
        $this->online_time = $online_time;

        return $this;
    }

}
