<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


class EquipmentSearch
{

    private ArrayCollection|null $categories;


    private ArrayCollection|null $types;


    private ArrayCollection|null $brands;


    private string|null $name;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->types = new ArrayCollection();
        $this->brands = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|null
     */
    public function getCategories(): ?ArrayCollection
    {
        return $this->categories;
    }

    /**
     * @param ArrayCollection|null $categories
     * @return EquipmentSearch
     */
    public function setCategories(?ArrayCollection $categories): EquipmentSearch
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getTypes(): ?ArrayCollection
    {
        return $this->types;
    }

    /**
     * @param ArrayCollection|null $types
     * @return EquipmentSearch
     */
    public function setTypes(?ArrayCollection $types): EquipmentSearch
    {
        $this->types = $types;
        return $this;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getBrands(): ?ArrayCollection
    {
        return $this->brands;
    }

    /**
     * @param ArrayCollection|null $brands
     * @return EquipmentSearch
     */
    public function setBrands(?ArrayCollection $brands): EquipmentSearch
    {
        $this->brands = $brands;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return EquipmentSearch
     */
    public function setName(?string $name): EquipmentSearch
    {
        $this->name = $name;
        return $this;
    }




}