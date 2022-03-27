<?php

namespace App\Controller\Admin;

use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/reglages")]
class AdminSettingsController extends AbstractController
{

    #[Route("", name: "admin.settings.index")]
    public function index(SettingsRepository $settingsRepository): Response
    {

        return $this->render('admin/settings/index.html.twig', [
            'menu' => 'admin.settings',
            'settings' => $settingsRepository->findAll()
        ]);
    }


}