<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Order;
use App\Entity\OrderState;
use App\Form\OrderType;
use App\Repository\ItemRepository;
use App\Repository\OrderRepository;
use App\Repository\StateRepository;
use App\Services\CartManagerService;
use App\Services\OrderManager;
use App\Services\UserNotifierService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OrderController extends AbstractController
{

    #[Route("/mes-commandes", name: "orders.user")]
    public function myOrders(StateRepository $stateRepository, OrderRepository $orderRepository): Response
    {
        return $this->render('pages/my-orders.html.twig', [
            'orders' => $orderRepository->findByUser($this->getUser()),
            'states' => $stateRepository->findAll(),
            'menu' => 'myOrder'
        ]);
    }

    #[Route("/mes-commandes/{id}", name: "orders.user.show")]
    public function myOrder(Order $order, StateRepository $stateRepository, OrderRepository $orderRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if($order->getClient()->getId() !== $this->getUser()->getId()) return $this->redirectToRoute('orders.user', [], Response::HTTP_SEE_OTHER);
        return $this->render('pages/show-order.html.twig', [
            'order' => $order,
            'menu' => 'myOrder'
        ]);
    }

    #[Route("/nouvelle-commande", name: "order.new")]
    public function new(Request $request, EntityManagerInterface $entityManager, OrderManager $orderManager, UserNotifierService $notifierService, CartManagerService $cartManagerService): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $order->setClient($this->getUser());
            $order->setEquipment($this->getUser()->getCartEquipment());

            $checkResult = $orderManager->checkConflicts($order);

            if($checkResult['hasConflict']){
                $this->addFlash('error', "Un ou plusieurs objets sélectionné ne sont pas disponible !");
                return $this->renderForm("pages/order/new.html.twig", [
                    'form' => $form,
                    'conflicts' => $checkResult['conflict'],
                ], (new Response())->setStatusCode(Response::HTTP_SEE_OTHER));
            }

            $order = $orderManager->createOrder($order, $checkResult['conflict']);

            $cartManagerService->flushUserCart();
            $entityManager->persist($order);
            $entityManager->flush();

            $notifierService->clientOrderReceived($order->getId());
            $notifierService->staffOrderReceived($order->getId());

            $this->addFlash('success', "Votre commande a été crée, vérifier vos emails !");
            return $this->redirectToRoute('orders.user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("pages/order/new.html.twig", [
            'form' => $form
        ]);
    }

}