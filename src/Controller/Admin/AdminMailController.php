<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderState;
use App\Form\AdminType\AdminTestEmailOrderFormType;
use App\Services\UserNotifierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/email", name: "admin.mail.", env: "dev")]
class AdminMailController extends AbstractController
{

    public function __construct(
        private UserNotifierService $userNotifierService,
    )
    {
    }

    #[Route("", name: "index")]
    public function index(): Response
    {
        return $this->render('admin/mail/index.html.twig', [
            'menu' => 'admin.mail'
        ]);
    }
    
    #[Route("/commande/nouveau-test", name: "order.test.new")]
    public function newTest(Request $request): Response
    {
        $order = new Order();
        $form = $this->createForm(AdminTestEmailOrderFormType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $order->setClient($this->getUser());
            $state = $form->get('state')->getData();
            $order->addOrderState((new OrderState())
                ->setCreatedBy($this->getUser())
                ->setComments("Ce statut a été généré automatiquement.")
                ->setState($state)
            );

            $this->userNotifierService->emailOrderTest($order);

            return $this->redirectToRoute('admin.mail.index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('admin/mail/order/new.html.twig', [
            'menu' => 'admin.mail',
            'form' => $form
        ]);


    }

}