<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item
{

    public const STATES = [
        0 => "Neuf",
        1 => "En bon état",
        2 => "Dommage esthétique",
        3 => "Dommage de fonctionnement",
        4 => "Innutilisable",
        5 => "En réparation",
        6 => "Épave"
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tag;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

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
     * @ORM\ManyToOne(targetEntity=Equipment::class, inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $equipment;

    /**
     * @ORM\ManyToMany(targetEntity=Order::class, mappedBy="items")
     */
    private $orders;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=UsedItem::class, mappedBy="item")
     */
    private $usedItems;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->setUpdatedAt(new \DateTimeImmutable());
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->usedItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getState(): ?int
    {
        return $this->state;
    }

    public function getStateText(): string
    {
        return self::STATES[$this->state];
    }

    public function setState(int $state): self
    {
        $this->state = $state;

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

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getEquipment().'#'.$this->getTag();
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
            $order->addItem($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $order->removeItem($this);
        }

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return Collection|UsedItem[]
     */
    public function getUsedItems(): Collection
    {
        return $this->usedItems;
    }

    public function addUsedItem(UsedItem $usedItem): self
    {
        if (!$this->usedItems->contains($usedItem)) {
            $this->usedItems[] = $usedItem;
            $usedItem->setItem($this);
        }

        return $this;
    }

    public function removeUsedItem(UsedItem $usedItem): self
    {
        if ($this->usedItems->removeElement($usedItem)) {
            // set the owning side to null (unless already changed)
            if ($usedItem->getItem() === $this) {
                $usedItem->setItem(null);
            }
        }

        return $this;
    }

}
