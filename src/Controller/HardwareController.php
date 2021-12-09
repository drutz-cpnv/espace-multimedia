<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/materiel")]
class HardwareController extends AbstractController
{

    #[Route("", name: "hardware")]
    public function index(): Response
    {

        return $this->render('pages/hardware.html.twig', [
            'menu' => 'hardware'
        ]);
    }

}