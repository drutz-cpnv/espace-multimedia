<?php

namespace App\Services\Tasks;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Services\IntranetAPI;
use Doctrine\ORM\EntityManagerInterface;

class UpdateRooms
{

    public function __construct(private IntranetAPI $intranetAPI, private EntityManagerInterface $entityManager, private RoomRepository $roomRepository)
    {
    }

    public function update()
    {
        $rooms = $this->intranetAPI->rooms();

        foreach ($rooms as $room)
        {
            if(!str_contains($room->friendly_id, 'sc-a')) continue;
            if(!is_null($this->roomRepository->findOneBy(['friendly_id' => $room->friendly_id]))) continue;

            $roomEntity = (new Room())
                ->setName($room->name)
                ->setDescription($room->description)
                ->setFriendlyId($room->friendly_id)
                ;

            $this->entityManager->persist($roomEntity);

        }

        $this->entityManager->flush();
    }

}