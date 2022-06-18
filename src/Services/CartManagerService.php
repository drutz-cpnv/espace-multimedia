<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\Equipment;
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

    public function toggleRoomEquipmentToCart(Equipment $equipment)
    {
        if($this->security->getUser()->isInCart($equipment)) {
            $this->entityManager->remove($this->security->getUser()->getEquipmentCart($equipment));
            $this->entityManager->flush();
            return;
        }

        $cart = (new Cart())
            ->setUser($this->security->getUser())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setEquipment($equipment)
            ->setQuantity(1);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

    }

}