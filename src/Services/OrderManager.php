<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\Internal\Conflict;
use App\Entity\Internal\Needed;
use App\Entity\Order;
use App\Entity\OrderState;
use App\Entity\State;
use App\Entity\User;
use App\Repository\ItemRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderStateRepository;
use App\Repository\StateRepository;
use DateInterval;
use DatePeriod;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderManager
{

    private const ACCEPT_ITEM_STATE = 4;

    private const NOT_AVAILABLE = [
        'late',
        'error',
        'terminated',
        'accepted',
        'in_progress',
        'pending'
    ];

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
        private FlashBagInterface $flashBag,
        private OrderRepository $orderRepository,
        private ItemRepository $itemRepository,
    )
    {
    }

    public static function getOrderPeriod(Order $order): DatePeriod
    {
        return new DatePeriod($order->getStart(), new DateInterval("P1D"), $order->getEnd()->modify("1 day"));
    }


    public static function periodsDays(DatePeriod $datePeriod): array
    {
        $output = [];
        foreach ($datePeriod as $day) {
            $output[] = $day;
        }
        return $output;
    }

    /**
     * @param Collection|Order[] $orders
     * @return array|Collection|Order[]
     */
    public function filterOrders(Collection|array $orders)
    {
        return $orders->filter(fn($v, $k) => in_array($v->getCurrentStatus()->getState()->getSlug(), self::NOT_AVAILABLE));
    }


    public function checkConflicts(Order $order): array
    {
        /** @var Cart[] $userCart */
        $userCart = $this->security->getUser()->getCarts();
        $needed = [];

        $out = [];

        $period = self::getOrderPeriod($order);
        $periodDays = self::periodsDays($period);

        foreach ($userCart as $cart) {
            $needed[] = (new Needed())
                ->setEquipment($cart->getEquipment())
                ->setItems($cart->getEquipment()->getItems()->toArray())
                ->setQuantity($cart->getQuantity())
                ;
        }

        foreach ($needed as &$need) {
            $items = $need->getItems();
            $need->setConflict((new Conflict())
                ->setName($need->getEquipment()->getName())
                ->setNeeded($need->getQuantity())
            )
            ;
            foreach ($items as $item) {
                if($item->getState() >= self::ACCEPT_ITEM_STATE) continue;

                if($item->getOrders()->isEmpty()) {
                    $need->addAvailableItem($item);
                    continue;
                }

                $need->getConflict()->newItemDates($item->getTag());

                $itemsOrders = self::filterOrders($item->getOrders());

                $ok = [];
                foreach ($itemsOrders as $itemsOrder) {
                    $orderPeriod = self::getOrderPeriod($itemsOrder);
                    $orderDays = self::periodsDays($orderPeriod);
                    foreach ($orderDays as $orderDay) {
                        if(in_array($orderDay, $periodDays)){
                            $need->getConflict()->addDate($item->getTag() ,$orderDay);
                            $ok[] = true;
                        }
                        else{
                            $ok[] = false;
                        }
                    }
                }

                if(in_array(true, $ok)) continue;
                $need->addAvailableItem($item);
            }
        }
        unset($need);

        foreach ($needed as $need) {
            $availableCount = count($need->getAvailableItems());
            $need->getConflict()
                ->setAvailable($availableCount)
                ->setAvailableItems($need->getAvailableItems())
            ;

            if($availableCount < $need->getQuantity()) {
                $need->getConflict()
                    ->setIsConflict(true)
                    ->setMessage("L'équipement {$need->getEquipment()->getName()} n'est disponible qu'en {$availableCount} exemplaires aux dates demandées.")
                ;
            }
            else {
                $need->getConflict()
                    ->setIsConflict(false)
                    ->setMessage("L'équipement {$need->getEquipment()->getName()} est disponible au nombre demander.")
                ;
            }

            $out['conflict'][] = $need->getConflict();
        }

        $out['hasConflict'] = false;
        foreach ($out['conflict'] as $result) {
            if ($out['hasConflict']) continue;
            $out['hasConflict'] = $result->isConflict();
        }

        return $out;

    }

    /**
     * @param Order $order
     * @param Conflict[] $conflicts
     * @return Order
     */
    public function createOrder(Order $order, array $conflicts): Order
    {
        foreach ($conflicts as $conflict) {
            $items = array_slice($conflict->getAvailableItems(), 0, $conflict->getNeeded());
            foreach ($items as $item) {
                $order->addItem($item);
            }
        }

        $order->addOrderState($this->getPendingState($this->security->getUser()));

        return $order;
    }




    /*public function checkConflicts(Order $order): array
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
    }*/

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

    public function verifyLateOrder()
    {
        $lateOrders = $this->orderRepository->findLate();
        dd($lateOrders);
    }

}