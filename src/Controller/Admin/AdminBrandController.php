<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/marques")]
class AdminBrandController extends AbstractController
{

    #[Route("", name: "admin.brand.index")]
    public function index(BrandRepository $brandRepository): Response
    {
        return $this->render('admin/brand/index.html.twig', [
            'menu' => 'admin.brand',
            'brands' => $brandRepository->findAll()
        ]);
    }

    #[Route("/modifier/{id}", name: "admin.brand.edit")]
    public function edit(Brand $brand, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $brand->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();
            return $this->redirectToRoute('admin.brand.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/brand/edit.html.twig', [
            'menu' => 'admin.brand',
            'brand' => $brand,
            'form' => $form
        ]);
    }

    #[Route("/new", name: "admin.brand.new")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($brand);
            $entityManager->flush();

            return $this->redirectToRoute('admin.brand.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/brand/new.html.twig', [
            'menu' => 'admin.brand',
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'admin.brand.delete', methods: ['POST'])]
    public function delete(Request $request, Brand $brand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            $entityManager->remove($brand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.brand.index', [], Response::HTTP_SEE_OTHER);
    }

}