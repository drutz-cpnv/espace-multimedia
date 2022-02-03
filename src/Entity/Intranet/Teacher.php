<?php

namespace App\Entity\Intranet;

class Teacher extends RootEntity
{

    private $external_uuid;
    private $firstname;
    private $lastname;
    private $email;
    private $acronym;


    /**
     * @return mixed
     */
    public function getExternalUuid()
    {
        return $this->external_uuid;
    }

    /**
     * @param mixed $external_uuid
     * @return Teacher
     */
    public function setExternalUuid($external_uuid)
    {
        $this->external_uuid = $external_uuid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     * @return Teacher
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     * @return Teacher
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Teacher
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAcronym()
    {
        return $this->acronym;
    }

    /**
     * @param mixed $acronym
     * @return Teacher
     */
    public function setAcronym($acronym)
    {
        $this->acronym = $acronym;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Teacher
     */
    public function setId(int $id): Teacher
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
     * @return Teacher
     */
    public function setFriendlyId(mixed $friendly_id): Teacher
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
     * @return Teacher
     */
    public function setType(mixed $type): Teacher
    {
        $this->type = $type;
        return $this;
    }



}