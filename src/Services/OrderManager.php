<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\OrderState;
use App\Entity\State;
use App\Entity\User;
use App\Repository\OrderStateRepository;
use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderManager
{

    private const ASSERT_TO_ACCEPT = [
        'late',
        'cancelled',
        'error',
        'terminated',
        'accepted'
    ];

    private const ASSERT_TO_REFUSE = [
        'late',
        'cancelled',
        'error',
        'terminated',
        'refused'
    ];

    public function __construct(
        private StateRepository $stateRepository,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private UserNotifierService $notifierService,
        private FlashBagInterface $flashBag
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

    public function canChangeState($current, $target)
    {
        if($current instanceof OrderState){
            $current = $current->getState()->getSlug();
        } elseif ($current instanceof Order){
            $current = $current->getCurrentStatus()->getState()->getSlug();
        }


        if($target instanceof State) {
            $target = $target->getSlug();
        }
        elseif ($target instanceof OrderState){
            $target = $target->getState()->getSlug();
        }

        $from = [
            'accepted' => [
                'refused',
                'cancelled',
                'in_progress',
                'pending',
            ],
            'pending' => [
                'refused',
                'accepted',
                'cancelled',
            ],
            'refused' => [
                'cancelled',
                'pending',
            ],
            'error' => [
                'cancelled',
                'late',
                'in_progress',
                'terminated',
            ],
            'late' => [
                'cancelled',
                'terminated',
            ],
            'terminated' => [],
            'in_progress' => [
                'cancelled',
                'error',
                'terminated',
            ],
            'cancelled' => [
                'pending'
            ]
        ];

        return in_array($target, $from[$current]);
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
        if (in_array($order->getCurrentStatus()->getState()->getSlug(), self::ASSERT_TO_ACCEPT)) return false;
        $accept = $this->stateRepository->findOneBySlug('accepted');
        $orderState->setState($accept);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
        $this->notifierService->clientOrderStatusChange($order->getId());
        return true;
    }

    public function refuseOrder(OrderState $orderState, Order $order)
    {
        if (in_array($order->getCurrentStatus()->getState()->getSlug(), self::ASSERT_TO_REFUSE)) return false;
        $refuse = $this->stateRepository->findOneBySlug('refused');
        $orderState->setState($refuse);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
        $this->notifierService->clientOrderStatusChange($order->getId());
        return true;
    }

    public function cancelOrder(OrderState $orderState, Order $order)
    {
        $cancel = $this->stateRepository->findOneBySlug('cancelled');
        $orderState->setState($cancel);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
        $this->notifierService->clientOrderStatusChange($order->getId());
    }

    public function equipmentPending(OrderState $orderState, Order $order)
    {
        $equipmentPending = $this->stateRepository->findOneBySlug('late');
        $orderState->setState($equipmentPending);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function invalidEquipment(OrderState $orderState, Order $order)
    {
        $equipmentPending = $this->stateRepository->findOneBySlug('error');
        $orderState->setState($equipmentPending);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function passOrder(OrderState $orderState, Order $order)
    {
        $equipmentPending = $this->stateRepository->findOneBySlug('terminated');
        $orderState->setState($equipmentPending);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function orderShipped(OrderState $orderState, Order $order)
    {
        $equipmentPending = $this->stateRepository->findOneBySlug('in_progress');
        $orderState->setState($equipmentPending);
        $orderState->setCreatedBy($this->security->getUser());
        $order->addOrderState($orderState);

        $this->entityManager->persist($orderState);
        $this->entityManager->flush();
    }

    public function changeState(string $stateSlug, OrderState &$orderState, Order $order)
    {
        $state = $this->stateRepository->findOneBySlug($stateSlug);
        if(is_null($state)){
            $this->flashBag->add('error', 'Une erreur est survenue !');
            return false;
        }

        if(!$this->canChangeState($order, $state)){
            $this->flashBag->add('error', 'La commande ne peut pas avoir ce statut actuellement !');
            return false;
        }

        $orderState->setState($state);
        $order->addOrderState($orderState);
        $orderState->setCreatedBy($this->security->getUser());
        $this->flashBag->add('success', 'Le statut de la commande a été modifié avec succès');

        return true;
    }

}