<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OrderController extends AbstractController
{

    #[Route("/mes-commandes", name: "orders.user")]
    public function myOrders(): Response
    {
        return $this->render('pages/my-orders.html.twig', [
            'menu' => 'myOrder'
        ]);
    }

}