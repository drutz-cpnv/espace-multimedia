<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderDocument;
use App\Form\AdminType\OrderDocumentType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $pdf->stream("test.pdf", [
            "Attachment" => true
        ]);

    }

    #[Route("/pdf/2/{id}", name: "admin.order.document.2")]
    public function generateOrderDocument2(Order $order)
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
        $pdf->stream("test.pdf", [
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

    #[Route("/{id}", name: "admin.order.show")]
    public function show(Order $order): Response
    {
        return $this->render('admin/orders/show.html.twig', [
            'menu' => 'admin.order',
            'order' => $order
        ]);
    }

}