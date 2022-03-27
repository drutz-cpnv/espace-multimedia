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
        if(in_array('ROLE_WEBMASTER', $user->getRoles())){
            $this->addFlash("error", "Cet utilisateur ne peut être modifier.");
            return $this->redirectToRoute('admin.index', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            return $this->redirectToRoute('admin.user.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'form' => $form,
            'menu' => 'admin.user',
            'user' => $user
        ]);
    }

}