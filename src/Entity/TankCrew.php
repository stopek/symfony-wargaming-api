<?php

namespace App\Entity;

use App\Repository\TankCrewRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TankCrewRepository::class)
 */
class TankCrew
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Tank::class, inversedBy="tankCrews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tank;

    public function getId(): ?int
    {
        return $this->id;
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
