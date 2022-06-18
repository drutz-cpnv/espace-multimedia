<?php

namespace App\Data;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MultipleUserRoleToggle
{

    private $role;

    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole(string $role): MultipleUserRoleToggle
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    /**
     * @param mixed $users
     * @return MultipleUserRoleToggle
     */
    public function setUsers(mixed $users): MultipleUserRoleToggle
    {
        $this->users = $users;
        return $this;
    }




}