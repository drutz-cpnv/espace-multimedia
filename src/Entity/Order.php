<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\GreaterThan("today")
     */
    private $start;

    /**
     * @ORM\Column(type="date_immutable")
     * @Assert\GreaterThanOrEqual(propertyPath="start")
     */
    private $end;

    /**
     * @ORM\ManyToMany(targetEntity=Item::class, inversedBy="orders", orphanRemoval=false)
     */
    private $items;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=OrderState::class, mappedBy="order", cascade={"persist"})
     */
    private $orderStates;

    /**
     * @ORM\OneToMany(targetEntity=OrderDocument::class, mappedBy="documet_order")
     */
    private $documents;

    /**
     * @ORM\ManyToOne(targetEntity=Teacher::class, inversedBy="orders")
     */
    private $teacher;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity=Equipment::class, inversedBy="orders")
     */
    private $equipment;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->orderStates = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->equipment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): User|UserInterface
    {
        return $this->client;
    }

    public function setClient(User|UserInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getStart(): ?DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): self
    {
        $this->start = DateTimeImmutable::createFromMutable($start);

        return $this;
    }

    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(\DateTime $end): self
    {

        $this->end = DateTimeImmutable::createFromMutable($end);

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
            $orderState->setOrder($this);
        }

        return $this;
    }

    public function removeOrderState(OrderState $orderState): self
    {
        if ($this->orderStates->removeElement($orderState)) {
            // set the owning side to null (unless already changed)
            if ($orderState->getOrder() === $this) {
                $orderState->setOrder(null);
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

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Equipment[]
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    /**
     * @param Equipment[]|Collection $equipments
     * @return Order
     */
    public function setEquipment(mixed $equipments)
    {
        foreach ($equipments as $equipment) {
            $this->addEquipment($equipment);
        }
        return $this;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment[] = $equipment;
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        $this->equipment->removeElement($equipment);

        return $this;
    }

    public function isConfilct(DateTimeImmutable $start, DateTimeImmutable $end)
    {
        $thisStart = $this->getStart()->getTimestamp();
        $thisEnd = $this->getEnd()->getTimestamp();
        return ($start->getTimestamp() >= $thisStart && $end->getTimestamp() <= $thisEnd) || ($start->getTimestamp() <= $thisEnd && $start->getTimestamp() >= $thisStart) || ($end->getTimestamp() >= $thisStart && $end->getTimestamp() <= $thisEnd) || ($start->getTimestamp() <= $thisStart && $end->getTimestamp() >= $thisEnd);
    }
}
