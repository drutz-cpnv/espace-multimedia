<?php

namespace App\Controller\Admin;


use App\Entity\User;
use App\Form\AdminType\AdminUserType;
use App\Repository\UserRepository;
use App\Services\IntranetAPI;
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
        $original = $user->getRoles();
        if(in_array('ROLE_WEBMASTER', $user->getRoles()) && !$this->isGranted('ROLE_WEBMASTER')){
            $this->addFlash("error", "Cet utilisateur ne peut être modifier.");
            return $this->redirectToRoute('admin.index', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(in_array('ROLE_WEBMASTER', $original)){
                $user->setRoles($original);
            }
            $user->setUpdatedBy($this->getUser());
            $entityManager->flush();
            return $this->redirectToRoute('admin.user.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'form' => $form,
            'menu' => 'admin.user',
            'user' => $user
        ]);
    }

    #[
        Route("/verify/{id}", name: "admin.user.verify"),
    ]
    public function verify(User $user, EntityManagerInterface $entityManager, IntranetAPI $intranetAPI): Response
    {
        $student = $intranetAPI->student($user->getEmail());

        if(is_null($student)) {
            $user->setStatus(3);
            $entityManager->flush();
            return $this->json(['verification' => ['result' => 'invalid', 'user' => $user->getId()]]);
        }
        return $this->json(['verification' => ['result' => 'valid', 'user' => $user->getId()]]);
    }

}