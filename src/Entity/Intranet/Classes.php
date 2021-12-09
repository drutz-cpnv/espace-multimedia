<?php

namespace App\Entity\Intranet;

use App\Services\IntranetAPI;

class Classes extends RootEntity
{

    private $name;

    /**
     * @var Student[]
     */
    private array $students;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Classes
     */
    public function setId(int $id): Classes
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFriendlyId(): mixed
    {
        return $this->friendly_id;
    }

    /**
     * @param mixed $friendly_id
     * @return Classes
     */
    public function setFriendlyId(mixed $friendly_id): Classes
    {
        $this->friendly_id = $friendly_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType(): mixed
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Classes
     */
    public function setType(mixed $type): Classes
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Classes
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array
    {
        return $this->students;
    }

    /**
     * @param Student[] $students
     * @return Classes
     */
    public function setStudents(array $students): Classes
    {
        $this->students = $students;
        return $this;
    }

    /*public function completeClass(IntranetAPI $intranetAPI)
    {
        $class = $intranetAPI->searchClass($this->getName());
    }*/




}