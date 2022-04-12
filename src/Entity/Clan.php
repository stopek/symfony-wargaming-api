<?php

namespace App\Entity;

use App\Repository\ClanRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ClanRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Clan extends EntityBase
{
    /**
     * @Groups({"clan_base", "api_data", "auth_user", "player_details", "tank_with_tank_exp"})
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"clan_base", "api_data"})
     * @ORM\Column(type="string", length=20)
     */
    private $clan_created_at;

    /**
     * @Groups({"clan_base", "api_data"})
     * @ORM\Column(type="smallint")
     */
    private $members_count;

    /**
     * @Groups({"clan_base", "api_data", "auth_user"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @Groups({"clan_base", "api_data", "auth_user", "g_player_clan_base_info", "tank_with_tank_exp"})
     * @ORM\Column(type="string", length=5)
     */
    private $tag;

    /**
     * @Groups({"api_data"})
     * @ORM\OneToMany(targetEntity=Player::class, mappedBy="clan", fetch="EXTRA_LAZY")
     */
    private $players;

    /** @ORM\Column(type="string", length=20, nullable=true) */
    private $clan_updated_at;


    /** @ORM\Column(type="datetime", nullable=true) */
    private $deleted_at;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $is_disbanded;

    /**
     * @Groups({"api_data"})
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="leader_clans")
     */
    private $leader;

    /**
     * @Groups({"api_data"})
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="creator_clans")
     */
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity=Search::class, mappedBy="Clan")
     */
    private $searches;

    /**
     * @Groups({"clan_base"})
     * @var int|float
     */
    private int|float $wn8;

    /**
     * @Groups({"clan_base"})
     * @var int
     */
    private int $active_players;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $updates;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->searches = new ArrayCollection();
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

    public function isClanUnpinPlayers(): bool
    {
        return $this->getIsDisbanded() || null !== $this->getDeletedAt();
    }

    public function getIsDisbanded(): ?bool
    {
        return $this->is_disbanded;
    }

    public function setIsDisbanded(?bool $is_disbanded): self
    {
        $this->is_disbanded = $is_disbanded;

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

    public function getClanCreatedAt(): ?string
    {
        return $this->clan_created_at;
    }

    public function setClanCreatedAt(string $clan_created_at): self
    {
        $this->clan_created_at = $clan_created_at;

        return $this;
    }

    public function getMembersCount(): ?int
    {
        return $this->members_count;
    }

    public function setMembersCount(int $members_count): self
    {
        $this->members_count = $members_count;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getClanUpdatedAt(): ?string
    {
        return $this->clan_updated_at;
    }

    public function setClanUpdatedAt(?string $clan_updated_at): self
    {
        $this->clan_updated_at = $clan_updated_at;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setClan($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getClan() === $this) {
                $player->setClan(null);
            }
        }

        return $this;
    }

    #[Pure]
    public function isUserCreatorOrLeader(Player $player): bool
    {
        return $this->isUserCreator($player) || $this->isUserLeader($player);
    }

    #[Pure]
    public function isUserCreator(Player $player): bool
    {
        return $this->getCreator() === $player;
    }

    public function getCreator(): ?Player
    {
        return $this->creator;
    }

    public function setCreator(?Player $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    #[Pure]
    public function isUserLeader(Player $player): bool
    {
        return $this->getLeader() === $player;
    }

    public function getLeader(): ?Player
    {
        return $this->leader;
    }

    public function setLeader(?Player $leader): self
    {
        $this->leader = $leader;

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
            $search->setClan($this);
        }

        return $this;
    }

    public function removeSearch(Search $search): self
    {
        if ($this->searches->removeElement($search)) {
            // set the owning side to null (unless already changed)
            if ($search->getClan() === $this) {
                $search->setClan(null);
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
     * @return int
     */
    public function getActivePlayers(): int
    {
        return $this->active_players;
    }

    /**
     * @param int $active_players
     */
    public function setActivePlayers(int $active_players): void
    {
        $this->active_players = $active_players;
    }

    public function getUpdates(): ?int
    {
        return $this->updates;
    }

    public function setUpdates(?int $updates): self
    {
        $this->updates = $updates;

        return $this;
    }
}
