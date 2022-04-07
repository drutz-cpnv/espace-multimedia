<?php

namespace App\Services;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CartManagerService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    )
    {
    }

    public function flushUserCart()
    {
        foreach ($this->security->getUser()->getCarts() as $cart) {
            $this->entityManager->remove($cart);
        }
    }

    public function addToCart()
    {
    }

}