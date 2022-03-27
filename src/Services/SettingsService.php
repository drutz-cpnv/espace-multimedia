<?php

namespace App\Services;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class SettingsService
{

    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }


    public function resetOrders()
    {
        $orders = $this->orderRepository->findAll();

        foreach ($orders as $order) {
            $this->entityManager->remove($order);
        }

        $this->entityManager->flush();
        $this->orderRepository->reset();
    }

}