<?php

namespace App\Entity\Internal;

use App\Entity\Equipment;
use App\Entity\Item;

class Needed
{

    private Equipment $equipment;

    private int $quantity;

    private array $items;

    private array $availableItems;

    private Conflict $conflict;

    public function __construct()
    {
        $this->availableItems = [];
    }

    /**
     * @return Equipment
     */
    public function getEquipment()
    {
        return $this->equipment;
    }

    /**
     * @param Equipment $equipment
     * @return Needed
     */
    public function setEquipment(Equipment $equipment)
    {
        $this->equipment = $equipment;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Needed
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Item[] $items
     * @return Needed
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return Item[]
     */
    public function getAvailableItems()
    {
        return $this->availableItems;
    }

    /**
     * @param Item[] $availableItems
     * @return Needed
     */
    public function setAvailableItems(array $availableItems)
    {
        $this->availableItems = $availableItems;
        return $this;
    }

    public function addAvailableItem(Item $item) {
        $this->availableItems[] = $item;
    }

    /**
     * @return Conflict
     */
    public function getConflict(): Conflict
    {
        return $this->conflict;
    }

    /**
     * @param Conflict $conflict
     * @return Needed
     */
    public function setConflict(Conflict $conflict): Needed
    {
        $this->conflict = $conflict;
        return $this;
    }





}