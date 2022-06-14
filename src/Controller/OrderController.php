<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Message;
use App\Entity\Order;
use App\Entity\OrderState;
use App\Form\MessageFormType;
use App\Form\OrderType;
use App\Repository\ItemRepository;
use App\Repository\OrderRepository;
use App\Repository\StateRepository;
use App\Services\CartManagerService;
use App\Services\OrderManager;
use App\Services\SettingsAccessCheckerService;
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

    public function __construct(
        private SettingsAccessCheckerService $accessCheckerService
    )
    {
    }

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
    public function myOrder(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if($order->getClient()->getId() !== $this->getUser()->getId()) return $this->redirectToRoute('orders.user', [], Response::HTTP_SEE_OTHER);
        return $this->render('pages/order/show-order.html.twig', [
            'order' => $order,
            'menu' => 'myOrder',
            'tab' => 'show.summary'
        ]);
    }

    #[Route("/mes-commandes/{id}/equipement", name: "orders.user.show.equipment")]
    public function myOrderEquipment(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if($order->getClient()->getId() !== $this->getUser()->getId()) return $this->redirectToRoute('orders.user', [], Response::HTTP_SEE_OTHER);
        return $this->render('pages/order/equipment-order.html.twig', [
            'order' => $order,
            'menu' => 'myOrder',
            'tab' => 'show.equipment'
        ]);
    }

    #[Route("/mes-commandes/{id}/error", name: "orders.user.show.error")]
    public function myOrderError(Order $order, EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if($order->getClient()->getId() !== $this->getUser()->getId() && !$this->isGranted('ROLE_ADMIN')) return $this->redirectToRoute('orders.user', [], Response::HTTP_SEE_OTHER);
        if(is_null($order->getErrorState())) return $this->redirectToRoute('orders.user.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        if(is_null($order->getErrorState()->getMessage())) {
            $this->addFlash('error', "Un problème est survenu veuillez contacter un administrateur.");
            return $this->redirectToRoute('orders.user.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        }
        $orderMessage = $order->getErrorState()->getMessage();

        if(!is_null($orderMessage->getMessage())) return $this->render('pages/order/error-order.html.twig', [
            'order' => $order,
            'menu' => 'myOrder',
            'tab' => 'show.error',
            'messageRequest' => $orderMessage
        ]);


        $message = (new Message())
            ->setTitle("Réponse à l'erreur de la commande n°". $orderMessage->getOrder()->getInitialZeroId())
            ->setCreatedBy($this->getUser())
        ;

        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $message
                ->setTitle("Réponse à l'erreur de la commande n°". $orderMessage->getOrder()->getInitialZeroId())
                ->setCreatedBy($this->getUser());
            $orderMessage->setMessage($message);
            $entityManager->flush();

            return $this->redirectToRoute('orders.user.show', ['id' => $orderMessage->getOrder()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pages/order/error-order_form.html.twig', [
            'order' => $order,
            'menu' => 'myOrder',
            'tab' => 'show.error',
            'form' => $form,
            'messageRequest' => $orderMessage
        ]);
    }

    #[Route("/mes-commandes/{id}/chronologie", name: "orders.user.show.chronology")]
    public function myOrderChronology(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if($order->getClient()->getId() !== $this->getUser()->getId()) return $this->redirectToRoute('orders.user', [], Response::HTTP_SEE_OTHER);
        return $this->render('pages/order/chronology-order.html.twig', [
            'order' => $order,
            'menu' => 'myOrder',
            'tab' => 'show.chronology'
        ]);
    }

    #[Route("/mes-commandes/{id}/action", name: "orders.user.show.actions")]
    public function myOrderActions(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if($order->getClient()->getId() !== $this->getUser()->getId() || !$this->isGranted('ROLE_ADMIN')) return $this->redirectToRoute('orders.user', [], Response::HTTP_SEE_OTHER);
        return $this->render('pages/order/show-order.html.twig', [
            'order' => $order,
            'menu' => 'myOrder',
            'tab' => 'show.actions'
        ]);
    }

    #[Route("/nouvelle-commande", name: "order.new")]
    public function new(Request $request, EntityManagerInterface $entityManager, OrderManager $orderManager, UserNotifierService $notifierService, CartManagerService $cartManagerService): Response
    {

        if(!$this->accessCheckerService->access('authorize.order.new', )) {
            $this->addFlash("error", "Les commandes ont été supendues temporairement.");
            return $this->redirectToRoute('user.cart', [], Response::HTTP_SEE_OTHER);
        }

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