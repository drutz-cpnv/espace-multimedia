<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\EquipmentTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @ORM\Entity(repositoryClass=EquipmentTypeRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get'],
    denormalizationContext: ['groups' => ['write:EquipmentType']],
    normalizationContext: ['groups' => ['read:EquipmentType']]
)]
class EquipmentType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:EquipmentType'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:EquipmentType', 'write:EquipmentType'])]
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Equipment::class, mappedBy="type")
     */
    private $equipments;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:EquipmentType', 'write:EquipmentType'])]
    private $slug;

    public function __construct()
    {
        $this->equipments = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()
    {
        $this->setSlug();
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

    /**
     * @return Collection|Equipment[]
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
            $equipment->setType($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipments->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getType() === $this) {
                $equipment->setType(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug = null): self
    {
        $slugger = new AsciiSlugger();
        if($slug === null) {
            $this->slug = strtolower($slugger->slug($this->getName()));
        }
        else{
            $this->slug = strtolower($slugger->slug($slug));
        }
        return $this;
    }
}
