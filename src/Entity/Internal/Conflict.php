<?php

namespace App\Entity\Internal;

use App\Entity\Item;
use DateTime;
use DateTimeImmutable;

class Conflict
{

    private bool $isConflict;

    private int $needed;

    private int $available;

    private string $message;

    private array $availableItems;

    private string $name;

    private array $dates;

    /**
     * @return bool
     */
    public function isConflict(): bool
    {
        return $this->isConflict;
    }

    /**
     * @param bool $isConflict
     * @return Conflict
     */
    public function setIsConflict(bool $isConflict): Conflict
    {
        $this->isConflict = $isConflict;
        return $this;
    }

    /**
     * @return int
     */
    public function getNeeded(): int
    {
        return $this->needed;
    }

    /**
     * @param int $needed
     * @return Conflict
     */
    public function setNeeded(int $needed): Conflict
    {
        $this->needed = $needed;
        return $this;
    }

    /**
     * @return int
     */
    public function getAvailable(): int
    {
        return $this->available;
    }

    /**
     * @param int $available
     * @return Conflict
     */
    public function setAvailable(int $available): Conflict
    {
        $this->available = $available;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Conflict
     */
    public function setMessage(string $message): Conflict
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return Item[]
     */
    public function getAvailableItems(): array
    {
        return $this->availableItems;
    }

    /**
     * @param Item[] $availableItems
     * @return Conflict
     */
    public function setAvailableItems(array $availableItems): Conflict
    {
        $this->availableItems = $availableItems;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Conflict
     */
    public function setName(string $name): Conflict
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    /**
     * @param array $dates
     * @return Conflict
     */
    public function setDates(array $dates): Conflict
    {
        $this->dates = $dates;
        return $this;
    }

    public function addDate($item, $dateTime)
    {
        $this->dates[$item][] = $dateTime;
    }

    public function newItemDates($item)
    {
        $this->dates[$item] = [];
    }



}