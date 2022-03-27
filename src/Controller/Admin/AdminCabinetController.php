<?php

namespace App\Controller\Admin;

use App\Entity\Cabinet;
use App\Form\AdminType\AdminCabinetFormType;
use App\Repository\CabinetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/armoire", name: "admin.cabinet.")]
class AdminCabinetController extends AbstractController
{

    #[Route("", name: "index")]
    public function index(CabinetRepository $cabinetRepository): Response
    {
        return $this->render('admin/cabinet/index.html.twig', [
            'menu' => 'admin.cabinet',
            'cabinets' => $cabinetRepository->findAll()
        ]);
    }

    #[Route("/{id}/modifier", name: "edit")]
    public function edit(Cabinet $cabinet, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminCabinetFormType::class, $cabinet);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('admin.cabinet.index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('admin/cabinet/edit.html.twig', [
            'form' => $form,
            'menu' => 'admin.cabinet',
        ]);
    }

    #[Route("/ajouter", name: "new")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cabinet = new Cabinet();
        $form = $this->createForm(AdminCabinetFormType::class, $cabinet);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cabinet);
            $entityManager->flush();
            return $this->redirectToRoute('admin.cabinet.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/cabinet/new.html.twig', [
            'menu' => 'admin.cabinet',
            'form' => $form
        ]);
    }

}