<?php

namespace App\Entity;

use App\Repository\TankRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TankRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Tank extends EntityBase
{
    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details", "expected_tanks", "moe_tanks", "g_tanks_base_list"})
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Groups({"tank_with_tank_exp", "api_data"})
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $short_name;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details", "g_tanks_base_list"})
     * @ORM\Column(type="integer")
     */
    private $price_gold;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details", "g_tanks_base_list"})
     * @ORM\Column(type="integer")
     */
    private $price_credit;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "expected_tanks", "moe_tanks", "player_details"})
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $nation;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details", "expected_tanks", "moe_tanks", "g_tanks_base_list"})
     * @ORM\Column(type="boolean")
     */
    private $is_premium;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details", "expected_tanks", "moe_tanks", "g_tanks_base_list"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details"})
     * @ORM\Column(type="string", length=100)
     */
    private $tag;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details", "g_tanks_base_list"})
     * @ORM\Column(type="integer")
     */
    private $prices_xp;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "expected_tanks", "moe_tanks", "player_details"})
     * @ORM\Column(type="smallint")
     */
    private $tier;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details", "expected_tanks", "moe_tanks"})
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "player_details", "expected_tanks", "moe_tanks", "g_tanks_base_list"})
     * @ORM\Column(type="string", length=200)
     */
    private $name;

    /** @ORM\OneToMany(targetEntity=TanksStats::class, mappedBy="tank") */
    private $tanksStats;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $large_image;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     */
    private $speed_forward;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     */
    private $speed_backward;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $armory = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     */
    private $free_xp_bonus;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $battle_level_max;

    /**
     * @ORM\OneToMany(targetEntity=TankCrew::class, mappedBy="tank", orphanRemoval=true)
     */
    private $tankCrews;

    /**
     * @Groups({"tank_with_tank_exp", "api_data"})
     * @ORM\OneToMany(targetEntity=ExpTanks::class, mappedBy="tank")
     */
    private $expTanks;

    /**
     * @Groups({"tank_with_tank_exp"})
     * @ORM\OneToMany(targetEntity=Stats::class, mappedBy="max_frags_tank")
     * @OrderBy({"max_frags" = "DESC"})
     */
    private $max_frags_stats;

    /**
     * @Groups({"tank_with_tank_exp"})
     * @ORM\OneToMany(targetEntity=Stats::class, mappedBy="max_xp_tank")
     * @OrderBy({"max_xp" = "DESC"})
     */
    private $max_xp_stats;

    /**
     * @Groups({"tank_with_tank_exp"})
     * @ORM\OneToMany(targetEntity=Stats::class, mappedBy="max_damage_tank")
     * @OrderBy({"max_damage" = "DESC"})
     */
    private $max_damage_stats;

    /**
     * @Groups({"tank_with_tank_exp"})
     * @ORM\OneToMany(targetEntity=MoeTanks::class, mappedBy="tank", orphanRemoval=true)
     */
    private $moeTanks;

    public function __construct()
    {
        $this->tanksStats = new ArrayCollection();
        $this->tankCrews = new ArrayCollection();
        $this->expTanks = new ArrayCollection();
        $this->max_frags_stats = new ArrayCollection();
        $this->max_xp_stats = new ArrayCollection();
        $this->max_damage_stats = new ArrayCollection();
        $this->moeTanks = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->short_name;
    }

    public function setShortName(?string $short_name): self
    {
        $this->short_name = $short_name;

        return $this;
    }

    public function getPriceGold(): ?int
    {
        return $this->price_gold;
    }

    public function setPriceGold(int $price_gold): self
    {
        $this->price_gold = $price_gold;

        return $this;
    }

    public function getPriceCredit(): ?int
    {
        return $this->price_credit;
    }

    public function setPriceCredit(int $price_credit): self
    {
        $this->price_credit = $price_credit;

        return $this;
    }

    public function getNation(): ?string
    {
        return $this->nation;
    }

    public function setNation(?string $nation): self
    {
        $this->nation = $nation;

        return $this;
    }

    public function getIsPremium(): ?bool
    {
        return $this->is_premium;
    }

    public function setIsPremium(bool $is_premium): self
    {
        $this->is_premium = $is_premium;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getPricesXp(): ?int
    {
        return $this->prices_xp;
    }

    public function setPricesXp(int $prices_xp): self
    {
        $this->prices_xp = $prices_xp;

        return $this;
    }

    public function getTier(): ?int
    {
        return $this->tier;
    }

    public function setTier(int $tier): self
    {
        $this->tier = $tier;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    /**
     * @return Collection|TanksStats[]
     */
    public function getTanksStats(): Collection
    {
        return $this->tanksStats;
    }

    public function addTanksStat(TanksStats $tanksStat): self
    {
        if (!$this->tanksStats->contains($tanksStat)) {
            $this->tanksStats[] = $tanksStat;
            $tanksStat->setTank($this);
        }

        return $this;
    }

    public function removeTanksStat(TanksStats $tanksStat): self
    {
        if ($this->tanksStats->removeElement($tanksStat)) {
            // set the owning side to null (unless already changed)
            if ($tanksStat->getTank() === $this) {
                $tanksStat->setTank(null);
            }
        }

        return $this;
    }

    public function getLargeImage(): ?string
    {
        return $this->large_image;
    }

    public function setLargeImage(?string $large_image): self
    {
        $this->large_image = $large_image;

        return $this;
    }

    public function getSpeedForward(): ?string
    {
        return $this->speed_forward;
    }

    public function setSpeedForward(?string $speed_forward): self
    {
        $this->speed_forward = $speed_forward;

        return $this;
    }

    public function getSpeedBackward(): ?string
    {
        return $this->speed_backward;
    }

    public function setSpeedBackward(?string $speed_backward): self
    {
        $this->speed_backward = $speed_backward;

        return $this;
    }

    public function getArmory(): ?array
    {
        return $this->armory;
    }

    public function setArmory(?array $armory): self
    {
        $this->armory = $armory;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getFreeXpBonus(): ?string
    {
        return $this->free_xp_bonus;
    }

    public function setFreeXpBonus(?string $free_xp_bonus): self
    {
        $this->free_xp_bonus = $free_xp_bonus;

        return $this;
    }

    public function getBattleLevelMax(): ?int
    {
        return $this->battle_level_max;
    }

    public function setBattleLevelMax(?int $battle_level_max): self
    {
        $this->battle_level_max = $battle_level_max;

        return $this;
    }

    /**
     * @return Collection|TankCrew[]
     */
    public function getTankCrews(): Collection
    {
        return $this->tankCrews;
    }

    public function addTankCrew(TankCrew $tankCrew): self
    {
        if (!$this->tankCrews->contains($tankCrew)) {
            $this->tankCrews[] = $tankCrew;
            $tankCrew->setTank($this);
        }

        return $this;
    }

    public function removeTankCrew(TankCrew $tankCrew): self
    {
        if ($this->tankCrews->removeElement($tankCrew)) {
            // set the owning side to null (unless already changed)
            if ($tankCrew->getTank() === $this) {
                $tankCrew->setTank(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ExpTanks[]
     */
    public function getExpTanks(): Collection
    {
        return $this->expTanks;
    }

    public function addExpTank(ExpTanks $expTank): self
    {
        if (!$this->expTanks->contains($expTank)) {
            $this->expTanks[] = $expTank;
            $expTank->setTank($this);
        }

        return $this;
    }

    public function removeExpTank(ExpTanks $expTank): self
    {
        if ($this->expTanks->removeElement($expTank)) {
            // set the owning side to null (unless already changed)
            if ($expTank->getTank() === $this) {
                $expTank->setTank(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Stats[]
     */
    public function getMaxFragsStats(): Collection
    {
        return $this->max_frags_stats;
    }

    public function addMaxFragsStat(Stats $maxFragsStat): self
    {
        if (!$this->max_frags_stats->contains($maxFragsStat)) {
            $this->max_frags_stats[] = $maxFragsStat;
            $maxFragsStat->setMaxFragsTank($this);
        }

        return $this;
    }

    public function removeMaxFragsStat(Stats $maxFragsStat): self
    {
        if ($this->max_frags_stats->removeElement($maxFragsStat)) {
            // set the owning side to null (unless already changed)
            if ($maxFragsStat->getMaxFragsTank() === $this) {
                $maxFragsStat->setMaxFragsTank(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Stats[]
     */
    public function getMaxXpStats(): Collection
    {
        return $this->max_xp_stats;
    }

    public function addMaxXpStat(Stats $maxXpStat): self
    {
        if (!$this->max_xp_stats->contains($maxXpStat)) {
            $this->max_xp_stats[] = $maxXpStat;
            $maxXpStat->setMaxXpTank($this);
        }

        return $this;
    }

    public function removeMaxXpStat(Stats $maxXpStat): self
    {
        if ($this->max_xp_stats->removeElement($maxXpStat)) {
            // set the owning side to null (unless already changed)
            if ($maxXpStat->getMaxXpTank() === $this) {
                $maxXpStat->setMaxXpTank(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Stats[]
     */
    public function getMaxDamageStats(): Collection
    {
        return $this->max_damage_stats;
    }

    public function addMaxDamageStat(Stats $maxDamageStat): self
    {
        if (!$this->max_damage_stats->contains($maxDamageStat)) {
            $this->max_damage_stats[] = $maxDamageStat;
            $maxDamageStat->setMaxDamageTank($this);
        }

        return $this;
    }

    public function removeMaxDamageStat(Stats $maxDamageStat): self
    {
        if ($this->max_damage_stats->removeElement($maxDamageStat)) {
            // set the owning side to null (unless already changed)
            if ($maxDamageStat->getMaxDamageTank() === $this) {
                $maxDamageStat->setMaxDamageTank(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MoeTanks[]
     */
    public function getMoeTanks(): Collection
    {
        return $this->moeTanks;
    }

    public function addMoeTank(MoeTanks $moeTank): self
    {
        if (!$this->moeTanks->contains($moeTank)) {
            $this->moeTanks[] = $moeTank;
            $moeTank->setTank($this);
        }

        return $this;
    }

    public function removeMoeTank(MoeTanks $moeTank): self
    {
        if ($this->moeTanks->removeElement($moeTank)) {
            // set the owning side to null (unless already changed)
            if ($moeTank->getTank() === $this) {
                $moeTank->setTank(null);
            }
        }

        return $this;
    }
}
