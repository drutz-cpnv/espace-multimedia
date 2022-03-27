<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Form\EquipmentSearchType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route("/profil", name: "user.profil")]
    public function index(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $fullUser = $userRepository->find($user->getId());
            $fullUser->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $fullUser,
                    $form->get('password')->getData()
                )
            );

            $entityManager->flush();
            $this->addFlash('success', "Votre profil a été mis à jour !");
            return $this->redirectToRoute('user.profil', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('pages/profil.html.twig', [
            'menu' => 'profil',
            'form' => $form
        ]);
    }

    #[Route("/cart", name: "user.cart")]
    public function confirmCart(): Response
    {
        return $this->render('pages/cart.html.twig');
    }

    #[Route("/cart/add/{id}", name: "user.cart.add", methods: ["POST"])]
    public function newCartItem(Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $user->addToCart($equipment);
        $entityManager->flush();
        $this->addFlash('success', "L'équipement a été ajouté à votre panier !");
        return $this->redirectToRoute('equipment.show', ['id' => $equipment->getId(), 'slug' => $equipment->getSlug()], Response::HTTP_SEE_OTHER);
    }

    #[Route("/cart/remove/{id}", name: "user.cart.remove", methods: ["POST"])]
    public function removeCartItem(Equipment $equipment, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $user->removeCart($equipment);
        $entityManager->flush();
        $this->addFlash('success', "L'équipement a été retiré de votre panier !");
        return $this->redirect($request->headers->get('referer'), Response::HTTP_SEE_OTHER);
    }

}