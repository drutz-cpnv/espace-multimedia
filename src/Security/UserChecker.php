<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if($user->getStatus() !== 1) {
            throw new CustomUserMessageAccountStatusException("Vous n'êtes pas autorisé vous connecter à votre compte. Veuillez contacter un administrateur pour en connaitre la raison.");
        } else {
            return;
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }
}