<?php

namespace App\Controller\Admin;

use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/salle-multimedia", name: "admin.room.")]
class AdminRoomController extends AbstractController
{

    #[Route("", name: "index")]
    public function index(EquipmentRepository $equipmentRepository): Response
    {
        $rooms = $equipmentRepository->findRoom();
        return $this->render("admin/room/index.html.twig", [
            'menu' => "admin.room.index",
            'rooms' => $rooms
        ]);
    }

}