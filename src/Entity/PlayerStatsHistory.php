<?php

namespace App\Entity;

use App\Repository\PlayerStatsHistoryRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlayerStatsHistoryRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class PlayerStatsHistory
{
    /**
     * @Groups({"g_player_stats_history"})
     * @ORM\Column(name="created_at", type="date", nullable=false)
     */
    protected $created_at;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="playerStatsHistories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;
    /**
     * @Groups({"g_player_stats_history"})
     * @ORM\Column(type="decimal", precision=10, scale=5, nullable=true)
     */
    private $wn8;
    /**
     * @Groups({"g_player_stats_history"})
     * @ORM\Column(type="decimal", precision=10, scale=5, nullable=true)
     */
    private $wn7;
    /**
     * @Groups({"g_player_stats_history"})
     * @ORM\Column(type="decimal", precision=10, scale=5, nullable=true)
     */
    private $efficiency;
    /**
     * @Groups({"g_player_stats_history"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $battles;
    /**
     * @Groups({"g_player_stats_history"})
     * @ORM\Column(type="decimal", precision=10, scale=5, nullable=true)
     */
    private $damage_ratio;
    /**
     * @Groups({"g_player_stats_history"})
     * @ORM\Column(type="decimal", precision=10, scale=5, nullable=true)
     */
    private $win_ratio;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(new DateTime('now'));
        }
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param DateTimeInterface $created_at
     * @return EntityBase
     *
     */
    public function setCreatedAt(DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getWn8(): ?string
    {
        return $this->wn8;
    }

    public function setWn8(?string $wn8): self
    {
        $this->wn8 = $wn8;

        return $this;
    }

    public function getWn7(): ?string
    {
        return $this->wn7;
    }

    public function setWn7(?string $wn7): self
    {
        $this->wn7 = $wn7;

        return $this;
    }

    public function getEfficiency(): ?string
    {
        return $this->efficiency;
    }

    public function setEfficiency(?string $efficiency): self
    {
        $this->efficiency = $efficiency;

        return $this;
    }

    public function getBattles(): ?int
    {
        return $this->battles;
    }

    public function setBattles(int $battles): self
    {
        $this->battles = $battles;

        return $this;
    }

    public function getDamageRatio(): ?float
    {
        return $this->damage_ratio;
    }

    public function setDamageRatio(float $damage_ratio): self
    {
        $this->damage_ratio = $damage_ratio;

        return $this;
    }

    public function getWinRatio(): ?string
    {
        return $this->win_ratio;
    }

    public function setWinRatio(?string $win_ratio): self
    {
        $this->win_ratio = $win_ratio;

        return $this;
    }
}
