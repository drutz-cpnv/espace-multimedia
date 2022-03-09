<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/tests")]
class WebmasterAreaController extends AbstractController
{

    #[Route("", name: "master.index")]
    public function index()
    {

    }

    #[Route("/user/by-role", name: "master.user.find-by-role")]
    public function findByRole(UserRepository $userRepository): Response
    {
        return $this->render('tests/findUser.html.twig', [
            'users' => $userRepository->findByRole("ROLE_EDITOR")
        ]);
    }


}