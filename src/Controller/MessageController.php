<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageFormType;
use App\Repository\OrderMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/message", name: "message.")]
class MessageController extends AbstractController
{

    #[Route("/r/{token}", name: "order.respond")]
    public function index(string $token, OrderMessageRepository $orderMessageRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $orderMessage = $orderMessageRepository->findOneByToken($token);

        if(is_null($orderMessage) || !is_null($orderMessage->getMessage())) {
            return $this->redirectToRoute("app.home", [], Response::HTTP_SEE_OTHER);
        }
        if($this->getUser()->getId() !== $orderMessage->getOrder()->getClient()->getId() || !$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute("app.home", [], Response::HTTP_SEE_OTHER);
        }

        $message = (new Message())
            ->setTitle("Réponse à l'erreur de la commande n°". $orderMessage->getOrder()->getInitialZeroId())
            ->setCreatedBy($this->getUser())
            ;

        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $message
                ->setTitle("Réponse à l'erreur de la commande n°". $orderMessage->getOrder()->getInitialZeroId())
                ->setCreatedBy($this->getUser());
            $orderMessage->setMessage($message);
            $entityManager->flush();

            return $this->redirectToRoute('orders.user.show', ['id' => $orderMessage->getOrder()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/order.new.html.twig', [
            'form' => $form,
            'messageRequest' => $orderMessage
        ]);
    }


}