<?php

namespace App\Controller\Admin;

use App\Entity\EquipmentType;
use App\Form\AdminType\AdminEquipmentTypeType;
use App\Repository\EquipmentTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/type")]
class AdminEquipmentTypeController extends AbstractController
{

    #[Route("", name: "admin.type.index")]
    public function index(EquipmentTypeRepository $typeRepository): Response
    {
        return $this->render("admin/type/index.html.twig", [
            'menu' => 'admin.type',
            'types' => $typeRepository->findAll()
        ]);
    }

    #[Route("/edit/{id}", name: "admin.type.edit")]
    public function edit(EquipmentType $equipmentType, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(AdminEquipmentTypeType::class, $equipmentType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $entityManager->flush();
            return $this->redirectToRoute('admin.type.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("admin/type/edit.html.twig", [
            'menu' => 'admin.type',
            'form' => $form
        ]);
    }

    #[Route("/new", name: "admin.type.new")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $type = new EquipmentType();
        $form = $this->createForm(AdminEquipmentTypeType::class, $type);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($type);
            $entityManager->flush();
            return $this->redirectToRoute('admin.type.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("admin/type/new.html.twig", [
            'menu' => 'admin.type',
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'admin.type.delete', methods: ['POST'])]
    public function delete(Request $request, EquipmentType $type, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$type->getId(), $request->request->get('_token'))) {
            $entityManager->remove($type);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.type.index', [], Response::HTTP_SEE_OTHER);
    }



}