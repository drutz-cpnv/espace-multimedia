<?php

namespace App\Controller\Admin;


use App\Entity\User;
use App\Form\AdminType\AdminUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/utilisateurs")]
class AdminUserController extends AbstractController
{

    #[Route("", name: "admin.user.index")]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'menu' => 'admin.user',
            'users' => $userRepository->findAll()
        ]);
    }

    #[Route("/edit/{id}", name: "admin.user.edit")]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->redirect('http://localhost:8000/admin/utilisateurs', 303);
            //$this->redirectToRoute('admin.user.index', [], 303);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'form' => $form,
            'menu' => 'admin.user',
            'user' => $user
        ]);
    }

}