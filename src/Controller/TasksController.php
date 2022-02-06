<?php

namespace App\Controller;

use App\Services\Tasks\UpdateTeachers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/tasks")]
class TasksController extends AbstractController
{

    public function updateTeachers(UpdateTeachers $teachers): Response
    {

    }

}