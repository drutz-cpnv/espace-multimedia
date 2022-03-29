<?php

namespace App\Entity;

use App\Repository\UsedItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsedItemRepository::class)
 */
class UsedItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="usedItems")
     */
    private $ordr;

    /**
     * @ORM\ManyToOne(targetEntity=Item::class, inversedBy="usedItems")
     */
    private $item;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdr(): ?Order
    {
        return $this->ordr;
    }

    public function setOrdr(?Order $ordr): self
    {
        $this->ordr = $ordr;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }
}
