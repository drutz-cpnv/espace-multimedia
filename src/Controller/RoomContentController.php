<?php

/**
 * TODO: Faire la page pour la cabine son, le fond vert et l'imprimante 3D
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/salle-multimedia")]
class RoomContentController extends AbstractController
{

    #[Route("", name: "room")]
    public function index(): Response
    {
        return $this->render('pages/room-content.html.twig', [
            'menu' => 'room'
        ]);
    }

}