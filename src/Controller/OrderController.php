<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Order;
use App\Entity\OrderState;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\StateRepository;
use App\Services\OrderManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route("/nouvelle-commande", name: "order.new")]
    public function new(Request $request, EntityManagerInterface $entityManager, OrderManager $orderManager, StateRepository $stateRepository): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $order->setClient($this->getUser());
            $order->setEquipment($this->getUser()->getCart());

            $check = $orderManager->checkConflicts($order);

            if(count($check['conflicts']) !== 0){
                return $this->render("pages/order/conflicts_order.html.twig", [
                    'conflicts' => $check['conflicts'],
                    'order' => $order
                ], (new Response())->setStatusCode(Response::HTTP_SEE_OTHER));
            }

            $order->addOrderState($orderManager->getPendingState($this->getUser()));

            $this->getUser()->flushCart();
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('equipment', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm("pages/order/new.html.twig", [
            'form' => $form
        ]);
    }

}