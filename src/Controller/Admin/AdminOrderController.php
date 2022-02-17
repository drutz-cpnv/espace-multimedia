<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderDocument;
use App\Entity\OrderState;
use App\Form\AdminType\AdminChangeStateOrderType;
use App\Form\AdminType\OrderDocumentType;
use App\Repository\OrderRepository;
use App\Repository\StateRepository;
use App\Services\OrderManager;
use App\Services\UserNotifierService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route("/admin/commandes")]
class AdminOrderController extends AbstractController
{

    #[Route("", name: "admin.order.index")]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('admin/orders/index.html.twig', [
            'menu' => 'admin.order',
            'orders' => $orderRepository->findAll()
        ]);
    }

    #[Route("/pdf/1/{id}", name: "admin.order.document.1")]
    public function generateOrderDocument1(Order $order)
    {

        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Ubuntu');

        $pdf = new Dompdf($pdfOptions);

        $content = $this->renderView('pdf/bdc_1.html.twig', [
            'order' => $order
        ]);

        $pdf->loadHtml($content);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $pdf->stream("Bon de livraison". $order->getInitialZeroId() . ".pdf", [
            "Attachment" => true
        ]);

    }

    #[Route("/pdf/2/{id}", name: "admin.order.document.2")]
    public function generateOrderDocument2(Order $order)
    {

        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Ubuntu');

        $pdf = new Dompdf($pdfOptions);

        $content = $this->renderView('pdf/bdc_2.html.twig', [
            'order' => $order
        ]);

        $pdf->loadHtml($content);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $pdf->stream("Bon de récupération_". $order->getInitialZeroId() . ".pdf", [
            "Attachment" => true
        ]);

    }

    #[Route("/{id}/order-document-form", name: "admin.order.upload.document", methods: ["POST", "GET"])]
    public function newOrderDocument(Order $order, Request $request, EntityManagerInterface $entityManager): Response
    {
        $document = new OrderDocument();
        $form = $this->createForm(OrderDocumentType::class, $document, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.upload.document', ['id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $document->setCreatedBy($this->getUser());
            $document->setDocumentOrder($order);
            $slugger = new AsciiSlugger();
            $document->setSlug($slugger->slug($document->getTitle() . "-" . $order->getInitialZeroId()));

            $entityManager->persist($document);
            $entityManager->flush();

            return $this->redirectToRoute("admin.order.show", ["id" => $order->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('admin/orders/document_form.html.twig', [
            'form' => $form
        ]);

    }

    #[Route("/accept/{id}", name: "admin.order.accept")]
    public function acceptOrder(Order $order, Request $request, OrderManager $orderManager): Response
    {
        $orderState = new OrderState();
        $form = $this->createForm(AdminChangeStateOrderType::class, $orderState, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.accept', ['id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $orderManager->acceptOrder($orderState, $order);
            return $this->redirectToRoute('admin.order.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/orders/state_change.html.twig', [
            'form' => $form,
            'form_title' => "Accepter la commande"
        ]);
    }

    #[Route("/pending/{id}", name: "admin.order.pending")]
    public function pendingOrder(Order $order, Request $request, OrderManager $orderManager): Response
    {
        $orderState = new OrderState();
        $form = $this->createForm(AdminChangeStateOrderType::class, $orderState, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.pending', ['id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $orderManager->pendingOrder($orderState, $order);
            return $this->redirectToRoute('admin.order.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/orders/state_change.html.twig', [
            'form' => $form,
            'form_title' => "Mettre en attente"
        ]);
    }

    #[Route("/refuse/{id}", name: "admin.order.refuse")]
    public function refuseOrder(Order $order, Request $request, OrderManager $orderManager): Response
    {
        $orderState = new OrderState();
        $form = $this->createForm(AdminChangeStateOrderType::class, $orderState, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.refuse', ['id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $orderManager->refuseOrder($orderState, $order);
            return $this->redirectToRoute('admin.order.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/orders/state_change.html.twig', [
            'form' => $form,
            'form_title' => "Refuser la commande"
        ]);
    }

    #[Route("/equipment-pending/{id}", name: "admin.order.pending_equipment")]
    public function equipmentPending(Order $order, Request $request, OrderManager $orderManager): Response
    {
        $orderState = new OrderState();
        $form = $this->createForm(AdminChangeStateOrderType::class, $orderState, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.pending_equipment', ['id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $orderManager->equipmentPending($orderState, $order);
            return $this->redirectToRoute('admin.order.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/orders/state_change.html.twig', [
            'form' => $form,
            'form_title' => "En attente de la récupération de l'équipement"
        ]);
    }

    #[Route("/cancel/{id}", name: "admin.order.cancel")]
    public function cancelOrder(Order $order, Request $request, OrderManager $orderManager): Response
    {
        $orderState = new OrderState();
        $form = $this->createForm(AdminChangeStateOrderType::class, $orderState, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.cancel', ['id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $orderManager->cancelOrder($orderState, $order);
            return $this->redirectToRoute('admin.order.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/orders/state_change.html.twig', [
            'form' => $form,
            'form_title' => "Annulation de la commande"
        ]);
    }

    #[Route("/equipment-invalid/{id}", name: "admin.order.invalid")]
    public function invalidEquipment(Order $order, Request $request, OrderManager $orderManager): Response
    {
        $orderState = new OrderState();
        $form = $this->createForm(AdminChangeStateOrderType::class, $orderState, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.invalid', ['id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $orderManager->invalidEquipment($orderState, $order);
            return $this->redirectToRoute('admin.order.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/orders/state_change.html.twig', [
            'form' => $form,
            'form_title' => "Equipement rendu incorrect"
        ]);
    }

    #[Route("/pass/{id}", name: "admin.order.pass")]
    public function passOrder(Order $order, Request $request, OrderManager $orderManager): Response
    {
        $orderState = new OrderState();
        $form = $this->createForm(AdminChangeStateOrderType::class, $orderState, [
            'attr' => [
                'action' => $this->generateUrl('admin.order.pass', ['id' => $order->getId()])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $orderManager->passOrder($orderState, $order);
            return $this->redirectToRoute('admin.order.show', ['id' => $order->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/orders/state_change.html.twig', [
            'form' => $form,
            'form_title' => "Matériel rendu"
        ]);
    }

    #[Route("/{id}", name: "admin.order.show")]
    public function show(Order $order, UserNotifierService $notifierService): Response
    {
        /*$sender = Address::create('Espace Multimédia <no-reply@espace-multimedia.drutz.ch>');
        $destination = Address::create('Dimitri RUTZ <dimitri.rutz@cpnv.ch>');

        $email = (new TemplatedEmail())
            ->from($sender)
            ->to($destination)
            ->subject("Nouvelle commande")
            ->htmlTemplate("email/order/new_client.html.twig")
            ->context([]);

        $mailer->send($email);*/

        //$notifierService->clientOrderReceived($this->getUser()->getId(), $order->getId());
        //$notifierService->clientOrderStatusChange($order->getId());

        return $this->render('admin/orders/show.html.twig', [
            'menu' => 'admin.order',
            'order' => $order
        ]);
    }

}