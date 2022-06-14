<?php

namespace App\Services;

use App\Data\MultipleItemsData;
use App\Entity\Item;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;

class ItemService
{

    public function __construct(
        private EntityManagerInterface $em,
        private ItemRepository $itemRepository,
        private Security $security,
    )
    {}

    public function createMultiple(MultipleItemsData $data)
    {
        for ($i = 1; $i <= $data->getCount(); $i++) {
            $tagNumber = $i >= 10 ? $i : str_pad(0, 2, (string)$i, STR_PAD_RIGHT);
            $tag = $data->getTag() . $tagNumber;
            if(is_null($this->itemRepository->findOneBy(['tag' => $tag]))) {
                $item = (new Item())
                    ->setTag($tag)
                    ->setCreatedBy($this->security->getUser())
                    ->setUpdatedBy($this->security->getUser())
                    ->setState(0)
                    ->setEquipment($data->getEquipment())
                    ->setUpdatedAt(new \DateTimeImmutable())
                    ->setCreatedAt(new \DateTimeImmutable());

                $this->em->persist($item);
            }
        }

        $this->em->flush();
    }

}