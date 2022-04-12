<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Game extends EntityBase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=22, nullable=true)
     */
    private $short_description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $awards_description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $rule_description;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="games")
     */
    private $organizer;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_private;

    /**
     * @ORM\OneToMany(targetEntity=GameSettings::class, mappedBy="game", orphanRemoval=true)
     */
    private $gameSettings;

    /**
     * @ORM\OneToMany(targetEntity=GamePlayer::class, mappedBy="game")
     */
    private $gamePlayers;

    public function __construct()
    {
        $this->gameSettings = new ArrayCollection();
        $this->gamePlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(?string $short_description): self
    {
        $this->short_description = $short_description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAwardsDescription(): ?string
    {
        return $this->awards_description;
    }

    public function setAwardsDescription(?string $awards_description): self
    {
        $this->awards_description = $awards_description;

        return $this;
    }

    public function getRuleDescription(): ?string
    {
        return $this->rule_description;
    }

    public function setRuleDescription(?string $rule_description): self
    {
        $this->rule_description = $rule_description;

        return $this;
    }

    public function getOrganizer(): ?Player
    {
        return $this->organizer;
    }

    public function setOrganizer(?Player $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getIsPrivate(): ?bool
    {
        return $this->is_private;
    }

    public function setIsPrivate(bool $is_private): self
    {
        $this->is_private = $is_private;

        return $this;
    }

    /**
     * @return Collection|GameSettings[]
     */
    public function getGameSettings(): Collection
    {
        return $this->gameSettings;
    }

    public function addGameSetting(GameSettings $gameSetting): self
    {
        if (!$this->gameSettings->contains($gameSetting)) {
            $this->gameSettings[] = $gameSetting;
            $gameSetting->setGame($this);
        }

        return $this;
    }

    public function removeGameSetting(GameSettings $gameSetting): self
    {
        if ($this->gameSettings->removeElement($gameSetting)) {
            // set the owning side to null (unless already changed)
            if ($gameSetting->getGame() === $this) {
                $gameSetting->setGame(null);
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
            $gamePlayer->setGame($this);
        }

        return $this;
    }

    public function removeGamePlayer(GamePlayer $gamePlayer): self
    {
        if ($this->gamePlayers->removeElement($gamePlayer)) {
            // set the owning side to null (unless already changed)
            if ($gamePlayer->getGame() === $this) {
                $gamePlayer->setGame(null);
            }
        }

        return $this;
    }
}
