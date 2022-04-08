<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Equipment;
use App\Entity\EquipmentSearch;
use App\Form\CartFormType;
use App\Form\EquipmentSearchType;
use App\Repository\EquipmentRepository;
use App\Repository\EquipmentTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function show(Equipment $equipment, string $slug, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($equipment->getSlug() !== $slug){
            return $this->redirectToRoute("equipment.show", [
                'slug' => $equipment->getSlug(),
                'id' => $equipment->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        $cart = new Cart();
        $cart->setEquipment($equipment);
        $form = $this->createForm(CartFormType::class, $cart);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if($this->getUser()->isInCart($equipment)) {
                $entityManager->remove($this->getUser()->getEquipmentCart($equipment));
            }
            else{
                $cart->setUser($this->getUser());
                $cart->setEquipment($equipment);
                $entityManager->persist($cart);
            }

            $entityManager->flush();

            return $this->redirectToRoute("equipment.show", [
                'slug' => $equipment->getSlug(),
                'id' => $equipment->getId()
            ], Response::HTTP_SEE_OTHER);

        }
        elseif($form->isSubmitted() && !$form->isValid()) {
            return $this->renderForm("pages/equipment_show.html.twig", [
                'menu' => 'hardware',
                'equipment' => $equipment,
                'cartForm' => $form
            ]);
        }

        return $this->renderForm("pages/equipment_show.html.twig", [
            'menu' => 'hardware',
            'equipment' => $equipment,
            'cartForm' => $form
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