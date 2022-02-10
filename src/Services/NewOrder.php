<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\OrderState;
use App\Entity\User;
use App\Repository\OrderStateRepository;
use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

class NewOrder
{

    public function __construct(
        private StateRepository $stateRepository
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


}