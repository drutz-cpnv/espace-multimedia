<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

class UserService
{


    public function toggleRole(string $role, User $user)
    {
        $userRoles = new ArrayCollection($user->getRoles());
        if($userRoles->contains($role)) {
            $this->removeRole($role, $user);
            return;
        }
        $this->addRole($role, $user);
    }

    public function addRole(string $role, User $user)
    {
        $userRoles = new ArrayCollection($user->getRoles());
        if(!$userRoles->contains($role)) {
            $userRoles->add($role);
            $user->setRoles($userRoles->toArray());
        }
    }

    public function removeRole(string $role, User $user)
    {
        $userRoles = new ArrayCollection($user->getRoles());
        if($userRoles->contains($role)) {
            $ri = $userRoles->indexOf($role);
            $userRoles->remove($ri);
            $user->setRoles($userRoles->toArray());
        }
    }
    
}