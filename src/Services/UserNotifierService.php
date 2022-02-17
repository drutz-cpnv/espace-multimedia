<?php

namespace App\Services;

use App\Entity\Content;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class UserNotifierService
{
    private Address $sender;

    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    )
    {
        $this->sender = Address::create('Espace Multimédia <no-reply@multimedia.mediamatique.ch>');
    }

    public function clientOrderReceived(int $orderId): bool
    {
        $order = $this->entityManager->find(Order::class, $orderId);
        if($order->getCurrentStatus()->getState()->getSlug() === "pending") {
            $content = $this->entityManager->find(Content::class, 3);
            $email = (new TemplatedEmail())
                ->subject("Confirmation de commande")
                ->from($this->sender)
                ->to($order->getClient()->getEmail())
                ->htmlTemplate('email/order/new_client.html.twig')
                ->context([
                    'content' => $content,
                    'order' => $order
                ]);

            $this->mailer->send($email);

            return true;
        }
        return false;
    }

    public function clientOrderStatusChange(int $orderId): bool
    {
        $order = $this->entityManager->find(Order::class, $orderId);
        $content = $order->getCurrentStatus()->getState()->getContentTemplate();
        if(!is_null($content)) {
            $subject = "Informations à propos de la commande N°".$order->getInitialZeroId();
            $email = (new TemplatedEmail())
                ->subject($subject)
                ->from($this->sender)
                ->to($order->getClient()->getEmail())
                ->htmlTemplate('email/order/simple.html.twig')
                ->context([
                    'title' => "Changement de statut",
                    'subject' => $subject,
                    'subject_header' => "Nouveau statut: ".$order->getCurrentStatus(),
                    'content' => $content,
                    'order' => $order
                ]);

            $this->mailer->send($email);

            return true;
        }
        return false;
    }

}