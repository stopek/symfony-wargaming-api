<?php

namespace App\Entity;

use App\Repository\ExpUpdateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ExpUpdateRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class ExpUpdate extends EntityBase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"api_data", "expected_tanks", "tank_update_version"})
     * @ORM\Column(type="string", length=255)
     */
    private $version;

    /** @ORM\Column(type="boolean") */
    private $is_active;

    /**
     * @Groups({"api_data", "expected_tanks"})
     * @ORM\OneToMany(targetEntity=ExpTanks::class, mappedBy="update_owner", orphanRemoval=true)
     */
    private $expTanks;

    public function __construct()
    {
        $this->expTanks = new ArrayCollection();
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

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

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
            $expTank->setUpdateOwner($this);
        }

        return $this;
    }

    public function removeExpTank(ExpTanks $expTank): self
    {
        if ($this->expTanks->removeElement($expTank)) {
            // set the owning side to null (unless already changed)
            if ($expTank->getUpdateOwner() === $this) {
                $expTank->setUpdateOwner(null);
            }
        }

        return $this;
    }
}
