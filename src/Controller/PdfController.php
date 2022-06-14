<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/pdf", name: "pdf.")]
class PdfController extends AbstractController
{

    #[Route("/order/admin/{id}", name: "order.admin")]
    public function orderAdmin(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $equipments = new ArrayCollection([]);

        foreach ($order->getEquipment() as $equipment) {
            $items = new ArrayCollection([]);
            foreach ($order->getItems() as $item) {
                if($equipment->getId() === $item->getEquipment()->getId()) {
                    $items[] = $item;
                }
            }
            $equipments->add([
                'items' => $items,
                'equipment' => $equipment
            ]);
        }

        return $this->render('pdf/bdc.html.twig', [
            'order' => $order,
            'equipments' => $equipments
        ]);
    }

    #[Route("/order/client/{id}", name: "order.client")]
    public function orderClient(Order $order): Response
    {
        $equipments = new ArrayCollection([]);

        foreach ($order->getEquipment() as $equipment) {
            $items = new ArrayCollection([]);
            foreach ($order->getItems() as $item) {
                if($equipment->getId() === $item->getEquipment()->getId()) {
                    $items[] = $item;
                }
            }
            $equipments->add([
                'items' => $items,
                'equipment' => $equipment
            ]);
        }

        return $this->render('pdf/bdc_client.html.twig', [
            'order' => $order,
            'equipments' => $equipments
        ]);
    }
}