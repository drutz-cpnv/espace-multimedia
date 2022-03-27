<?php

namespace App\Services;

use App\Entity\Content;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class UserNotifierService
{

    public function __construct(
        private MailerService $mailerService,
        private EntityManagerInterface $entityManager,
        private ContentRepository $contentRepository
    )
    {
    }

    public function clientOrderReceived(int $orderId): bool
    {
        $order = $this->entityManager->find(Order::class, $orderId);
        if($order->getCurrentStatus()->getState()->getSlug() === "pending") {
            $content = $order->getCurrentStatus()->getState()->getContentTemplate();
            $email = (new TemplatedEmail())
                ->subject("Commande N°{$order->getInitialZeroId()}: Reçue")
                ->to($order->getClient()->getEmail())
                ->htmlTemplate('email/order/new_client.html.twig')
                ->context([
                    'content' => $content,
                    'order' => $order
                ]);

            $this->mailerService->send($email);

            return true;
        }
        return false;
    }

    public function clientOrderStatusChange(int $orderId): bool
    {
        $order = $this->entityManager->find(Order::class, $orderId);
        $content = $order->getCurrentStatus()->getState()->getContentTemplate();
        if(!is_null($content)) {
            $subject = "Mise à jour de la commande N°{$order->getInitialZeroId()}";
            $email = (new TemplatedEmail())
                ->subject($subject)
                ->to($order->getClient()->getEmail())
                ->htmlTemplate('email/order/simple.html.twig')
                ->context([
                    'title' => "Changement de statut",
                    'subject' => $subject,
                    'subject_header' => "Nouveau statut: {$order->getCurrentStatus()}",
                    'content' => $content,
                    'order' => $order
                ]);

            $this->mailerService->send($email);

            return true;
        }
        return false;
    }

    public function staffOrderReceived(int $orderId) {
        $order = $this->entityManager->find(Order::class, $orderId);
        if($order->getCurrentStatus()->getState()->getSlug() === "pending") {
            $userRepository = $this->entityManager->getRepository(User::class);
            $content = $this->contentRepository->findOneByKey('email.order.staff.new');
            $staff = $userRepository->findByRoles(['ROLE_WEBMASTER']);
            $email = (new TemplatedEmail())
                ->subject("Une nouvelle commande a été effectuée (n°{$order->getInitialZeroId()})")
                ->htmlTemplate('email/order/new_staff.html.twig')
                ->context([
                    'content' => $content,
                    'order' => $order
                ]);

            foreach ($staff as $user) {
                $email->to($user->getEmail());
                $this->mailerService->send($email);
            }

            return true;
        }
        return false;
    }

}