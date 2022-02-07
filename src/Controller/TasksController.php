<?php

namespace App\Controller;

use App\Services\Tasks\UpdateTeachers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/tasks")]
class TasksController extends AbstractController
{

    #[Route("/update-teachers", name: "tasks.update.teachers")]
    public function updateTeachers(UpdateTeachers $teachers): Response
    {
        $teachers->update();
        return $this->redirectToRoute('admin.index', [], Response::HTTP_SEE_OTHER);
    }

}