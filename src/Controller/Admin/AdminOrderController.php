<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Dompdf\Css\Style;
use Dompdf\Css\Stylesheet;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

}