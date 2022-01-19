<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $start;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $end;

    /**
     * @ORM\ManyToMany(targetEntity=Item::class, inversedBy="orders")
     */
    private $items;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=OrderState::class, mappedBy="order_id")
     */
    private $orderStates;

    /**
     * @ORM\OneToMany(targetEntity=OrderDocument::class, mappedBy="documet_order")
     */
    private $documents;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->orderStates = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(\DateTimeImmutable $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(\DateTimeImmutable $end): self
    {
        $this->end = $end;

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
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        $this->items->removeElement($item);

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
     * @return Collection|OrderState[]
     */
    public function getOrderStates(): Collection
    {
        return $this->orderStates;
    }

    public function addOrderState(OrderState $orderState): self
    {
        if (!$this->orderStates->contains($orderState)) {
            $this->orderStates[] = $orderState;
            $orderState->setOrderId($this);
        }

        return $this;
    }

    public function removeOrderState(OrderState $orderState): self
    {
        if ($this->orderStates->removeElement($orderState)) {
            // set the owning side to null (unless already changed)
            if ($orderState->getOrderId() === $this) {
                $orderState->setOrderId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrderDocument[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(OrderDocument $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setDocumetOrder($this);
        }

        return $this;
    }

    public function removeDocument(OrderDocument $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getDocumetOrder() === $this) {
                $document->setDocumetOrder(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getClient() . " - " . $this->getCreatedAt()->format("d.m.Y H:i");
    }
}
