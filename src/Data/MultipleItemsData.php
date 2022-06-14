<?php

namespace App\Data;

use App\Entity\Equipment;

class MultipleItemsData
{

    private ?int $count = 1;
    private ?string $tag = "";
    private ?Equipment $equipment = null;

    /**
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int|null $count
     * @return MultipleItemsData
     */
    public function setCount(?int $count): MultipleItemsData
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param string|null $tag
     * @return MultipleItemsData
     */
    public function setTag(?string $tag): MultipleItemsData
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return Equipment|null
     */
    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    /**
     * @param Equipment|null $equipment
     * @return MultipleItemsData
     */
    public function setEquipment(?Equipment $equipment): MultipleItemsData
    {
        $this->equipment = $equipment;
        return $this;
    }



}