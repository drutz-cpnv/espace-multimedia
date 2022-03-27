<?php

namespace App\Controller\Admin;

use App\Repository\StateRepository;
use App\Repository\UserRepository;
use App\Services\ContentManager;
use App\Services\SetupService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route("/admin")]
class AdminController extends AbstractController
{

    #[Route("", name: "admin.index")]
    public function index(ChartBuilderInterface $chartBuilder, StateRepository $stateRepository, SetupService $setupService): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $data = [];
        $color = [];
        $label = [];

        foreach ($stateRepository->findAll() as $state) {
            $data[] = $state->getOrderCount();
            $color[] = "#".$state->getColor();
            $label[] = $state->getName();
        }

        $chart->setData([
            'labels' => $label,
            'datasets' => [
                [
                    'label' => "Commandes",
                    'data' => $data,
                    'backgroundColor' => $color
                ]
            ]
        ]);

        $chart->setOptions([
            "plugins" => ['legend' => [
                'display' => false
            ],]
        ]);


        return $this->render('admin/index.html.twig', [
            'menu' => 'home',
            'chart' => $chart
        ]);
    }

}