<?php

namespace App\Controller;

use App\Entity\UserType;
use App\Repository\UserRepository;
use App\Services\IntranetAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(IntranetAPI $API): Response
    {
        return $this->render('pages/index.html.twig', [
            'menu' => 'home'
        ]);
    }

    #[Route('/aide', name: 'support')]
    public function support(): Response
    {
        return $this->render('pages/index.html.twig', [
            'menu' => 'support'
        ]);
    }
}
