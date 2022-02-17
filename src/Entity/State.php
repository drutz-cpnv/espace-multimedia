<?php

namespace App\Entity;

use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StateRepository::class)
 */
class State
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
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=OrderState::class, mappedBy="state")
     */
    private $orderStates;

    /**
     * @ORM\ManyToOne(targetEntity=Content::class)
     */
    private $content_template;

    public function __construct()
    {
        $this->orderStates = new ArrayCollection();
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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
            $orderState->setState($this);
        }

        return $this;
    }

    public function removeOrderState(OrderState $orderState): self
    {
        if ($this->orderStates->removeElement($orderState)) {
            // set the owning side to null (unless already changed)
            if ($orderState->getState() === $this) {
                $orderState->setState(null);
            }
        }

        return $this;
    }

    public function getOrderCount()
    {
        $orders = [];
        foreach ($this->getOrderStates() as $orderState) {
            $order = $orderState->getOrder();
            if($order->getCurrentStatus() === $orderState){
                $orders[] = $order;
            }
        }
        return count($orders);

    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getContentTemplate(): ?Content
    {
        return $this->content_template;
    }

    public function setContentTemplate(?Content $content_template): self
    {
        $this->content_template = $content_template;

        return $this;
    }
}
