<?php

namespace App\Controller\Admin;

use App\Data\MultipleItemsData;
use App\Entity\Equipment;
use App\Entity\Item;
use App\Form\AdminType\AdminEquipmentType;
use App\Form\AdminType\AdminShortItemType;
use App\Form\MultipleItemsFormType;
use App\Repository\EquipmentRepository;
use App\Services\ItemService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/equipement")]
class AdminEquipmentController extends AbstractController
{

    #[Route("", name: "admin.equipment.index")]
    public function index(EquipmentRepository $equipmentRepository): Response
    {
        return $this->render('admin/equipment/index.html.twig', [
            'menu' => 'admin.equipment',
            'equipments' => $equipmentRepository->findAllEquipment()
        ]);
    }

    #[Route("/{id}/edit", name: "admin.equipment.edit")]
    public function edit(Equipment $equipment, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminEquipmentType::class, $equipment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $equipment->setUpdatedBy($this->getUser());
            $equipment->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->flush();

            return $this->redirectToRoute('admin.equipment.index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('admin/equipment/edit.html.twig', [
            'menu' => 'admin.equipment',
            'form' => $form
        ]);
    }

    #[Route("/new", name: "admin.equipment.new")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipment = new Equipment();
        $form = $this->createForm(AdminEquipmentType::class, $equipment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $equipment->setCreatedBy($this->getUser());
            $equipment->setUpdatedBy($this->getUser());

            $entityManager->persist($equipment);
            $entityManager->flush();

            return $this->redirectToRoute('admin.equipment.index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('admin/equipment/new.html.twig', [
            'menu' => 'admin.equipment',
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'admin.equipment.delete', methods: ['POST'])]
    public function delete(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($equipment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.equipment.index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/{id}", name: "admin.equipment.show")]
    public function show(Equipment $equipment): Response
    {
        return $this->render('admin/equipment/show.html.twig', [
            'menu' => 'admin.equipment',
            'equipment' => $equipment
        ]);
    }


    #[Route("/{id}/getform", name: "admin.equipment.itemform")]
    public function newItem(Equipment $equipment, Request $request, EntityManagerInterface $entityManager): Response
    {
        $item = new Item();
        $form = $this->createForm(AdminShortItemType::class, $item, [
            'attr' => [
                'action' => $this->generateUrl('admin.equipment.itemform', ['id' => $equipment->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $item->setEquipment($equipment);
            $item->setCreatedBy($this->getUser());
            $item->setUpdatedBy($this->getUser());
            $equipment->setUpdatedBy($this->getUser());

            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute('admin.equipment.show', [ 'id' => $equipment->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('admin/equipment/_item_short_form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/{id}/ajouter-plusieurs-objets", name: "admin.equipment.add_multiple_items")]
    public function newMultipleItem(Equipment $equipment, Request $request, ItemService $itemService): Response
    {
        $items = new MultipleItemsData();
        $form = $this->createForm(MultipleItemsFormType::class, $items);
        $form->handleRequest($request);
        $items->setEquipment($equipment);

        if($form->isSubmitted() && $form->isValid()){
            $itemService->createMultiple($items);
            return $this->redirectToRoute('admin.equipment.show', [ 'id' => $equipment->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/equipment/multiple_item_add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/objet/{id}/delete', name: 'admin.item.delete', methods: ['POST'])]
    public function deleteItem(Request $request, Item $item, EntityManagerInterface $entityManager): Response
    {
        $equipment = $item->getEquipment()->getId();
        if ($this->isCsrfTokenValid('delete'.$item->getId(), $request->request->get('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.equipment.show', ['id' => $equipment], Response::HTTP_SEE_OTHER);
    }






}

