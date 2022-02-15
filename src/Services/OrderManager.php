<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\OrderState;
use App\Entity\User;
use App\Repository\OrderStateRepository;
use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderManager
{

    public function __construct(
        private StateRepository $stateRepository,
        private EntityManagerInterface $entityManager,
        private Security $security
    )
    {
    }

    public function checkConflicts(Order $order): array
    {
        $conflicts = new ArrayCollection();
        $usedItems = new ArrayCollection();

        foreach ($order->getEquipment() as $equipment) {
            $equipmentItems = $equipment->getItems()->toArray();

            if($equipment->getOrders()->isEmpty()) {
                $order->addItem($equipmentItems[0]);
                continue;
            }

            foreach ($equipment->getItems() as $key => $item) {
                foreach ($item->getOrders() as $itemOrder){
                    if ($itemOrder->isConfilct($order->getStart(), $order->getEnd())){
                        unset($equipmentItems[$key]);
                        $usedItems->add($item);
                        if (count($equipmentItems) !== 0) {
                            $order->addItem($equipmentItems[$key+1]);
                        }
                        else{
                            $conflicts->add([$equipment, $itemOrder]);
                        }
                        continue;
                    }

                    if (count($equipmentItems) !== 0) {
                        $order->addItem($item);
                    }
                    else{
                        $conflicts->add([$equipment, $itemOrder]);
                    }
                }
            }
        }

        return [
            'conflicts' => $conflicts,
            'usedItems' => $usedItems
        ];
    }

    public function getPendingState(UserInterface|User $user)
    {
        $pending = $this->stateRepository->findOneBySlug('pending');

        return (new OrderState())
            ->setState($pending)
            ->setCreatedBy($user)
            ->setComments("Création de la commande -> elle passe par conséquent au status \"En attente\".");
    }

    public function pendingOrder(OrderState $orderState, Order $order)
    {
        $accept = $this->stateRepository->findOneBySlug('pending');
        $orderState->setState($accept);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function acceptOrder(OrderState $orderState, Order $order)
    {
        $accept = $this->stateRepository->findOneBySlug('accepted');
        $orderState->setState($accept);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function refuseOrder(OrderState $orderState, Order $order)
    {
        $refuse = $this->stateRepository->findOneBySlug('refused');
        $orderState->setState($refuse);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function cancelOrder(OrderState $orderState, Order $order)
    {
        $cancel = $this->stateRepository->findOneBySlug('cancelled');
        $orderState->setState($cancel);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function equipmentPending(OrderState $orderState, Order $order)
    {
        $equipmentPending = $this->stateRepository->findOneBySlug('equipment_pending');
        $orderState->setState($equipmentPending);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function invalidEquipment(OrderState $orderState, Order $order)
    {
        $equipmentPending = $this->stateRepository->findOneBySlug('equipment_invalid');
        $orderState->setState($equipmentPending);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function passOrder(OrderState $orderState, Order $order)
    {
        $equipmentPending = $this->stateRepository->findOneBySlug('passed');
        $orderState->setState($equipmentPending);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

}