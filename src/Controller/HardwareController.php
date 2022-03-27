<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\EquipmentSearch;
use App\Form\EquipmentSearchType;
use App\Repository\EquipmentRepository;
use App\Repository\EquipmentTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/equipement")]
class HardwareController extends AbstractController
{

    #[Route("", name: "equipment")]
    public function index(EquipmentRepository $equipmentRepository, Request $request): Response
    {
        $search = new EquipmentSearch();
        $form = $this->createForm(EquipmentSearchType::class, $search);
        $form->handleRequest($request);
        $equipments = $equipmentRepository->findSearch($search);

        return $this->renderForm('pages/hardware.html.twig', [
            'menu' => 'hardware',
            'equipments' => $equipments,
            'form' => $form
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