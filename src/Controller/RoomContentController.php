<?php

/**
 * TODO: Faire la page pour la cabine son, le fond vert et l'imprimante 3D
 */

namespace App\Controller;

use App\Entity\Equipment;
use App\Repository\EquipmentRepository;
use App\Services\CartManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/salle-multimedia")]
class RoomContentController extends AbstractController
{

    #[Route("", name: "room")]
    public function index(EquipmentRepository $equipmentRepository): Response
    {
        $rooms = $equipmentRepository->findRoom();
        return $this->render('pages/room-content.html.twig', [
            'menu' => 'room',
            'rooms' => $rooms
        ]);
    }

    #[Route("/{id}", name: "room.toggle")]
    public function toggle(Equipment $equipment, CartManagerService $cartManagerService): Response
    {
        if(!$equipment->getIsRoom()) return $this->redirectToRoute('app.home', [], Response::HTTP_SEE_OTHER);
        $cartManagerService->toggleRoomEquipmentToCart($equipment);
        return $this->redirectToRoute('room', [], Response::HTTP_SEE_OTHER);
    }

}