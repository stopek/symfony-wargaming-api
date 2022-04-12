<?php

namespace App\Entity;

use App\Repository\SearchRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SearchRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Search extends EntityBase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="searches")
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity=Clan::class, inversedBy="searches")
     */
    private $Clan;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $string;

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

    public function getClan(): ?Clan
    {
        return $this->Clan;
    }

    public function setClan(?Clan $Clan): self
    {
        $this->Clan = $Clan;

        return $this;
    }

    public function getString(): ?string
    {
        return $this->string;
    }

    public function setString(?string $string): self
    {
        $this->string = $string;

        return $this;
    }
}
