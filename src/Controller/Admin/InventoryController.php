<?php

/**
 * TODO: Minifier les objets dans les armoires par Equipement
 */

namespace App\Controller\Admin;

use App\Repository\CabinetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/inventaire", name: "admin.inventory.")]
class InventoryController extends AbstractController
{

    #[Route("", name: "index")]
    public function index(CabinetRepository $cabinetRepository): Response
    {
        return $this->render('admin/inventory/index.html.twig', [
            'menu' => "admin.inventory",
            'cabinets' => $cabinetRepository->findAll()
        ]);
    }

}