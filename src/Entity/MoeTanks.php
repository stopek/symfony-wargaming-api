<?php

namespace App\Entity;

use App\Repository\MoeTanksRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MoeTanksRepository::class)
 */
class MoeTanks
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"tank_with_tank_exp"})
     * @ORM\ManyToOne(targetEntity=MoeUpdate::class, inversedBy="moeTanks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $update_owner;

    /**
     * @Groups({"moe_tanks", "tank_with_tank_exp"})
     * @ORM\Column(type="decimal", precision=16, scale=12)
     */
    private $moe_1dmg;

    /**
     * @Groups({"moe_tanks", "tank_with_tank_exp"})
     * @ORM\Column(type="decimal", precision=16, scale=12)
     */
    private $moe_2dmg;

    /**
     * @Groups({"moe_tanks", "tank_with_tank_exp"})
     * @ORM\Column(type="decimal", precision=16, scale=12)
     */
    private $moe_3dmg;

    /**
     * @Groups({"moe_tanks", "tank_with_tank_exp"})
     * @ORM\Column(type="integer")
     */
    private $battles;

    /**
     * @Groups({"moe_tanks"})
     * @ORM\ManyToOne(targetEntity=Tank::class, inversedBy="moeTanks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tank;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUpdateOwner(): ?MoeUpdate
    {
        return $this->update_owner;
    }

    public function setUpdateOwner(?MoeUpdate $update_owner): self
    {
        $this->update_owner = $update_owner;

        return $this;
    }

    public function getMoe1dmg(): ?string
    {
        return $this->moe_1dmg;
    }

    public function setMoe1dmg(string $moe_1dmg): self
    {
        $this->moe_1dmg = $moe_1dmg;

        return $this;
    }

    public function getMoe2dmg(): ?string
    {
        return $this->moe_2dmg;
    }

    public function setMoe2dmg(string $moe_2dmg): self
    {
        $this->moe_2dmg = $moe_2dmg;

        return $this;
    }

    public function getMoe3dmg(): ?string
    {
        return $this->moe_3dmg;
    }

    public function setMoe3dmg(string $moe_3dmg): self
    {
        $this->moe_3dmg = $moe_3dmg;

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
