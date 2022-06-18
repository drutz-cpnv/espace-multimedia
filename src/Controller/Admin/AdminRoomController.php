<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use App\Form\AdminType\AdminMultipleUserRoleToggleType;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route("/{id}", name: "edit")]
    public function edit(Equipment $equipment, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(AdminMultipleUserRoleToggleType::class, $equipment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $equipment->setUpdatedBy($this->getUser());
            $equipment->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('admin.room.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("admin/room/edit.html.twig", [
            'menu' => "admin.room.index",
            'form' => $form
        ]);
    }

}