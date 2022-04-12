<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route("/{id}", name: "admin.settings.switch", methods: ["POST"])]
    public function changeSetting(Settings $settings, EntityManagerInterface $entityManager): Response
    {
        $settings->setValue((string)!$settings->isActive());
        $entityManager->flush();
        return $this->redirectToRoute('admin.settings.index', [], Response::HTTP_SEE_OTHER);
    }


}