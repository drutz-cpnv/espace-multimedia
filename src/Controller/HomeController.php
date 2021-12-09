<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(): Response
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
