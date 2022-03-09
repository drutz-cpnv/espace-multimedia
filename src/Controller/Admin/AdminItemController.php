<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use App\Form\AdminType\AdminItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/objet")]
class AdminItemController extends AbstractController
{

    #[Route("/{id}", name: "admin.item.edit")]
    public function edit(Item $item, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminItemType::class, $item);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $item->setUpdatedAt(new \DateTimeImmutable());
            $item->setUpdatedBy($this->getUser());

            $entityManager->flush();

            return $this->redirectToRoute('admin.equipment.show', ['id' => $item->getEquipment()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/equipment/edit_item.html.twig', [
            'menu' => 'admin.equipment',
            'form' => $form
        ]);
    }

}