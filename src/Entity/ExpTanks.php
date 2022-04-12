<?php

namespace App\Entity;

use App\Repository\ExpTanksRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ExpTanksRepository::class)
 */
class ExpTanks
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"tank_update_version"})
     * @ORM\ManyToOne(targetEntity=ExpUpdate::class, inversedBy="expTanks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $update_owner;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "expected_tanks"})
     * @ORM\Column(type="decimal", precision=16, scale=12)
     */
    private $damage;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "expected_tanks"})
     * @ORM\Column(type="decimal", precision=16, scale=12)
     */
    private $def;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "expected_tanks"})
     * @ORM\Column(type="decimal", precision=16, scale=12)
     */
    private $frag;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $tier;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "expected_tanks"})
     * @ORM\Column(type="decimal", precision=16, scale=12)
     */
    private $win;

    /**
     * @Groups({"tank_with_tank_exp", "api_data", "expected_tanks"})
     * @ORM\Column(type="decimal", precision=16, scale=12)
     */
    private $spot;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tag;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nation;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $image;

    /**
     * @Groups({"api_data", "expected_tanks"})
     * @ORM\ManyToOne(targetEntity=Tank::class, inversedBy="expTanks")
     */
    private $tank;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUpdateOwner(): ?ExpUpdate
    {
        return $this->update_owner;
    }

    public function setUpdateOwner(?ExpUpdate $update_owner): self
    {
        $this->update_owner = $update_owner;

        return $this;
    }

    public function getDamage(): ?string
    {
        return $this->damage;
    }

    public function setDamage(string $damage): self
    {
        $this->damage = $damage;

        return $this;
    }

    public function getDef(): ?string
    {
        return $this->def;
    }

    public function setDef(string $def): self
    {
        $this->def = $def;

        return $this;
    }

    public function getFrag(): ?string
    {
        return $this->frag;
    }

    public function setFrag(string $frag): self
    {
        $this->frag = $frag;

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

    public function getTier(): ?int
    {
        return $this->tier;
    }

    public function setTier(int $tier): self
    {
        $this->tier = $tier;

        return $this;
    }

    public function getWin(): ?string
    {
        return $this->win;
    }

    public function setWin(string $win): self
    {
        $this->win = $win;

        return $this;
    }

    public function getSpot(): ?string
    {
        return $this->spot;
    }

    public function setSpot(string $spot): self
    {
        $this->spot = $spot;

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

    public function getNation(): ?string
    {
        return $this->nation;
    }

    public function setNation(string $nation): self
    {
        $this->nation = $nation;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTank(): ?Tank
    {
        return $this->tank;
    }

    public function setTank(?Tank $tank): self
    {
        $this->tank = $tank;

        return $this;
    }
}
