<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Repository\EquipmentRepository;
use App\Repository\EquipmentTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/equipement")]
class HardwareController extends AbstractController
{

    #[Route("", name: "equipment")]
    public function index(EquipmentRepository $equipmentRepository): Response
    {
        return $this->render('pages/hardware.html.twig', [
            'menu' => 'hardware',
            'equipments' => $equipmentRepository->findAllEnabled()
        ]);
    }



    #[Route("/{slug<[a-z0-9A-Z\-]+>}-{id<\d+>}", name: "equipment.show", priority: 10)]
    public function show(Equipment $equipment, string $slug, EquipmentRepository $equipmentRepository): Response
    {
        if ($equipment->getSlug() !== $slug){
            return $this->redirectToRoute("equipment.show", [
                'slug' => $equipment->getSlug(),
                'id' => $equipment->getId()
            ]);
        }

        return $this->render("pages/equipment_show.html.twig", [
            'menu' => 'hardware',
            'equipment' => $equipment
        ]);
    }

    #[Route("/{slug<[a-z0-9A-Z\-]+>}", name: "equipment.type.show", priority: 9)]
    public function showType(string $slug, EquipmentTypeRepository $typeRepository): Response
    {
        $type = $typeRepository->findOneBySlug($slug);
        if ($type === null){
            return $this->redirectToRoute("equipment", [], Response::HTTP_SEE_OTHER);
        }

        $types = $typeRepository->findAll();

        return $this->render("pages/equipment_type_show.html.twig", [
            'menu' => 'hardware',
            'type' => $type,
            'types' => $types
        ]);
    }

}