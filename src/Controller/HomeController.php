<?php

namespace App\Controller;

use App\Entity\UserType;
use App\Repository\UserRepository;
use App\Services\IntranetAPI;
use App\Services\Tasks\UpdateTeachers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'app.home')]
    public function index(): Response
    {


        return $this->render('pages/index.html.twig', [
            'menu' => 'home'
        ]);
    }

    #[Route('/aide', name: 'app.support')]
    public function support(): Response
    {
        return $this->render('pages/index.html.twig', [
            'menu' => 'support'
        ]);
    }
}
