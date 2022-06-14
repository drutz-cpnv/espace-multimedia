<?php

namespace App\Controller;

use App\Entity\UserType;
use App\Repository\ContentRepository;
use App\Repository\EquipmentRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use App\Services\IntranetAPI;
use App\Services\Tasks\UpdateTeachers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'app.home')]
    public function index(EquipmentRepository $equipmentRepository): Response
    {
        $last = $equipmentRepository->findLast();
        return $this->render('pages/index.html.twig', [
            'menu' => 'home',
            'equipments' => $last
        ]);
    }

    #[Route('/informations', name: 'app.support')]
    public function support(ContentRepository $contentRepository): Response
    {
        return $this->render('pages/support.html.twig', [
            'menu' => 'support',
            'content' => $contentRepository->findOneByKey("page.support")
        ]);
    }
}
