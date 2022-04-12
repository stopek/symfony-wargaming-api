<?php

namespace App\Entity;

use App\Repository\MapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MapRepository::class)
 */
class Map
{
    /**
     * @ORM\Id
     * @Groups("api_data")
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("api_data")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Groups("api_data")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $video;

    /**
     * @ORM\OneToMany(targetEntity=MapGenerator::class, mappedBy="map", orphanRemoval=true)
     */
    private $mapGenerators;

    public function __construct()
    {
        $this->mapGenerators = new ArrayCollection();
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

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @return Collection|MapGenerator[]
     */
    public function getMapGenerators(): Collection
    {
        return $this->mapGenerators;
    }

    public function addMapGenerator(MapGenerator $mapGenerator): self
    {
        if (!$this->mapGenerators->contains($mapGenerator)) {
            $this->mapGenerators[] = $mapGenerator;
            $mapGenerator->setMap($this);
        }

        return $this;
    }

    public function removeMapGenerator(MapGenerator $mapGenerator): self
    {
        if ($this->mapGenerators->removeElement($mapGenerator)) {
            // set the owning side to null (unless already changed)
            if ($mapGenerator->getMap() === $this) {
                $mapGenerator->setMap(null);
            }
        }

        return $this;
    }
}
