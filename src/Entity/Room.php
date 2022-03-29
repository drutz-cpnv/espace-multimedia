<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
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
     * @ORM\Column(type="string", length=255)
     */
    private $friendly_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Cabinet::class, mappedBy="location")
     */
    private $cabinets;


    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->cabinets = new ArrayCollection();
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

    public function getFriendlyId(): ?string
    {
        return $this->friendly_id;
    }

    public function setFriendlyId(string $friendly_id): self
    {
        $this->friendly_id = $friendly_id;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Cabinet[]
     */
    public function getCabinets(): Collection
    {
        return $this->cabinets;
    }

    public function addCabinet(Cabinet $cabinet): self
    {
        if (!$this->cabinets->contains($cabinet)) {
            $this->cabinets[] = $cabinet;
            $cabinet->setLocation($this);
        }

        return $this;
    }

    public function removeCabinet(Cabinet $cabinet): self
    {
        if ($this->cabinets->removeElement($cabinet)) {
            // set the owning side to null (unless already changed)
            if ($cabinet->getLocation() === $this) {
                $cabinet->setLocation(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

}
