<?php

namespace App\Entity\Intranet;

use DateTime;

class Student extends RootEntity
{

    /**
     * firstname API Field
     * @var string
     */
    private $firstname;

    /**
     * lastname API Field
     * @var string
     */
    private $lastname;


    private $external_uuid;


    private $occupation;


    private $fullname;


    private $phone_token;


    private $email;


    private DateTime $phone_token_sent_at;


    private DateTime $phone_verified_at;


    private DateTime $updated_at;

    private ?Classes $class;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Student
     */
    public function setId(int $id): Student
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
     * @return Student
     */
    public function setFriendlyId(mixed $friendly_id): Student
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
     * @return Student
     */
    public function setType(mixed $type): Student
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Student
     */
    public function setFirstname(string $firstname): Student
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Student
     */
    public function setLastname(string $lastname): Student
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExternalUuid()
    {
        return $this->external_uuid;
    }

    /**
     * @param mixed $external_uuid
     * @return Student
     */
    public function setExternalUuid($external_uuid)
    {
        $this->external_uuid = $external_uuid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * @param mixed $occupation
     * @return Student
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param mixed $fullname
     * @return Student
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneToken()
    {
        return $this->phone_token;
    }

    /**
     * @param mixed $phone_token
     * @return Student
     */
    public function setPhoneToken($phone_token)
    {
        $this->phone_token = $phone_token;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPhoneTokenSentAt(): DateTime
    {
        return $this->phone_token_sent_at;
    }

    /**
     * @param DateTime $phone_token_sent_at
     * @return Student
     */
    public function setPhoneTokenSentAt(DateTime $phone_token_sent_at): Student
    {
        $this->phone_token_sent_at = $phone_token_sent_at;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPhoneVerifiedAt(): DateTime
    {
        return $this->phone_verified_at;
    }

    /**
     * @param DateTime $phone_verified_at
     * @return Student
     */
    public function setPhoneVerifiedAt(DateTime $phone_verified_at): Student
    {
        $this->phone_verified_at = $phone_verified_at;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param DateTime $updated_at
     * @return Student
     */
    public function setUpdatedAt(DateTime $updated_at): Student
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return Classes|null
     */
    public function getClass(): ?Classes
    {
        return $this->class;
    }

    /**
     * @param Classes $class
     * @return Student
     */
    public function setClass(Classes $class): Student
    {
        $this->class = $class;
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
     * @return Student
     */
    public function setEmail($email)
    {
        $this->email = strtolower($email);
        return $this;
    }

    public function issetClass(): bool
    {
        return isset($this->class);
    }




}