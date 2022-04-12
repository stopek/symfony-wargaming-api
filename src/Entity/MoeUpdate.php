<?php

namespace App\Entity;

use App\Repository\MoeUpdateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MoeUpdateRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class MoeUpdate extends EntityBase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"tank_with_tank_exp"})
     * @ORM\Column(type="string", length=10)
     */
    private $version;

    /**
     * @ORM\OneToMany(targetEntity=MoeTanks::class, mappedBy="update_owner", orphanRemoval=true)
     */
    private $moeTanks;

    public function __construct()
    {
        $this->moeTanks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

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
            $moeTank->setUpdateOwner($this);
        }

        return $this;
    }

    public function removeMoeTank(MoeTanks $moeTank): self
    {
        if ($this->moeTanks->removeElement($moeTank)) {
            // set the owning side to null (unless already changed)
            if ($moeTank->getUpdateOwner() === $this) {
                $moeTank->setUpdateOwner(null);
            }
        }

        return $this;
    }
}
