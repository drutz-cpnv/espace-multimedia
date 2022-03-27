<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=EquipmentRepository::class)
 * @Vich\Uploadable()
 */
class Equipment
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Assert\Image(
     *     mimeTypes={"image/png", "image/jpg", "image/jpeg"}
     * )
     * @Vich\UploadableField(mapping="equipment_image", fileNameProperty="filename")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $updatedBy;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="equipment")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="equipment")
     */
    private $brand;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity=Item::class, mappedBy="equipment", orphanRemoval=true)
     */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentType::class, inversedBy="equipments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=Order::class, mappedBy="equipment")
     */
    private $orders;

    /**
     * @ORM\ManyToOne(targetEntity=Cabinet::class, inversedBy="equipment")
     */
    private $cabinet;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->setUpdatedAt(new \DateTimeImmutable());
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setEnabled(false);
        $this->orders = new ArrayCollection();
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): User|UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User|UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): User|UserInterface
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(User|UserInterface $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return Equipment
     */
    public function setFilename(?string $filename): Equipment
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return Equipment
     */
    public function setImageFile(?File $imageFile): Equipment
    {
        $this->imageFile = $imageFile;
        if($this->imageFile instanceof UploadedFile){
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setEquipment($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getEquipment() === $this) {
                $item->setEquipment(null);
            }
        }

        return $this;
    }

    public function getItemCount()
    {
        return $this->getItems()->count();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getType(): ?EquipmentType
    {
        return $this->type;
    }

    public function setType(?EquipmentType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSlug(): string
    {
        $slugger = new AsciiSlugger();
        return strtolower($slugger->slug($this->getName()));
    }

    public function getCategoriesCS()
    {
        $string = "";
        $last = $this->categories->count() - 1;
        /** @var Category $category */
        foreach ($this->categories as $key => $category){
            if($key === $last){
                $string .= $category->getName();
            }
            elseif($key === $last-1) {
                $string .= $category->getName() . " et ";
            }
            else{
                $string .= $category->getName() . ", ";
            }
        }
        return $string;
    }

    public function getSimilar(): ArrayCollection
    {
        $similar = new ArrayCollection();
        /** @var Category $category */
        foreach ($this->categories as $category){
            foreach ($category->getEquipment() as $equipment) {
                if(!$similar->contains($equipment) && $equipment !== $this){
                    $similar->add($equipment);
                }
            }
        }

        return $similar;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->addEquipment($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $order->removeEquipment($this);
        }

        return $this;
    }

    public function getCabinet(): ?Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(?Cabinet $cabinet): self
    {
        $this->cabinet = $cabinet;

        return $this;
    }


}
