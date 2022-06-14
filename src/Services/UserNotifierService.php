<?php

namespace App\Services;

use App\Entity\Content;
use App\Entity\Order;
use App\Entity\OrderMessage;
use App\Entity\State;
use App\Entity\User;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;
use function PHPUnit\Framework\returnArgument;

class UserNotifierService
{

    public function __construct(
        private MailerService $mailerService,
        private EntityManagerInterface $entityManager,
        private ContentRepository $contentRepository,
        private UrlGeneratorInterface $urlGenerator,
        private Security $security
    )
    {
    }

    public function clientOrderReceived(int $orderId): bool
    {
        $order = $this->entityManager->find(Order::class, $orderId);
        if($order->getCurrentStatus()->getState()->getSlug() === "pending") {
            $content = $order->getCurrentStatus()->getState()->getContentTemplate();
            $email = $this->getClientNewOrder($order, $content);

            $this->mailerService->send($email);
        }
        return false;
    }

    public function clientOrderStatusChange(int $orderId): bool
    {
        $order = $this->entityManager->find(Order::class, $orderId);
        $content = $order->getCurrentStatus()->getState()->getContentTemplate();
        if(!is_null($content)) {
            $email = $this->getClientUpdatedOrderState($order, $content);
            return $this->mailerService->send($email);
        }
        return false;
    }

    public function clientOrderError(int $orderId): bool
    {
        $order = $this->entityManager->find(Order::class, $orderId);
        $content = $order->getCurrentStatus()->getState()->getContentTemplate();

        $orderMessage = (new OrderMessage())
            ->setCreatedBy($order->getCurrentStatus()->getCreatedBy())
            ->setOrderState($order->getCurrentStatus())
            ->setToken(sha1($order->getId() . $order->getClient()->getFullname() . time()))
        ;

        $this->entityManager->persist($orderMessage);
        $this->entityManager->flush();

        if(!is_null($content)) {
            $email = $this->getClientErrorMail($order, $content, false, $orderMessage);
            $this->mailerService->send($email);

            return true;
        }
        return false;
    }

    public function staffOrderReceived(int $orderId) {
        $order = $this->entityManager->find(Order::class, $orderId);
        if($order->getCurrentStatus()->getState()->getSlug() === "pending") {
            $userRepository = $this->entityManager->getRepository(User::class);
            $staff = $userRepository->findByRoles(['ROLE_WEBMASTER', 'ROLE_ADMIN', 'ROLE_EDITOR']);
            $email = $this->getStaffNewOrder($order);

            foreach ($staff as $user) {
                $email->to($user->getAddress());
                $this->mailerService->send($email);
            }

            return true;
        }
        return false;
    }

    public function emailOrderTest(Order $order)
    {
        $emails = [];
        $currentStateSlug = $order->getCurrentStatus()->getState()->getSlug();
        $content = $order->getCurrentStatus()->getState()->getContentTemplate();
        if($currentStateSlug === 'error') {
            $emails[] = $this->getClientErrorMail($order, $content, true);
        } elseif ($currentStateSlug === 'pending') {
            $emails[] = ($this->getStaffNewOrder($order)->to($order->getClient()->getEmail()));
            $emails[] = $this->getClientUpdatedOrderState($order, $content);
            $emails[] = $this->getClientNewOrder($order, $content);
        } else {
            $emails[] = $this->getClientUpdatedOrderState($order, $content);
        }

        foreach ($emails as $email) {
            $this->mailerService->send($email);
        }

    }


    private function getClientErrorMail(Order $order, Content $content, bool $isTest = false, OrderMessage $orderMessage = null): TemplatedEmail
    {
        $initZero = $order->getInitialZeroId() ?? 'xxxxx';
        $subject = "Une erreur est survenue avec la commande N°{$initZero}";
        return (new TemplatedEmail())
            ->subject($subject)
            ->to($order->getClient()->getAddress())
            ->htmlTemplate('email/order/error.html.twig')
            ->context([
                'title' => "Changement de statut",
                'subject' => $subject,
                'subject_header' => "Nouveau statut: {$order->getCurrentStatus()}",
                'content' => $content,
                'order' => $order,
                'errorLink' => $isTest ? '#' : $this->urlGenerator->generate('message.order.respond', ['token' => $orderMessage->getToken()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);
    }

    private function getClientUpdatedOrderState(Order $order, Content $content): TemplatedEmail
    {
        $initZero = $order->getInitialZeroId() ?? 'xxxxx';
        $subject = "Mise à jour de la commande N°{$initZero}";
        return (new TemplatedEmail())
            ->subject($subject)
            ->to($order->getClient()->getAddress())
            ->htmlTemplate('email/order/simple.html.twig')
            ->context([
                'title' => "Changement de statut",
                'subject' => $subject,
                'subject_header' => "Nouveau statut: {$order->getCurrentStatus()}",
                'content' => $content,
                'order' => $order
            ]);
    }

    private function getStaffNewOrder(Order $order): TemplatedEmail
    {
        $initZero = $order->getInitialZeroId() ?: 'xxxxx';
        $content = $this->contentRepository->findOneByKey('email.order.staff.new');
        return (new TemplatedEmail())
            ->subject("Une nouvelle commande a été effectuée (n°$initZero)")
            ->htmlTemplate('email/order/new_staff.html.twig')
            ->context([
                'content' => $content,
                'order' => $order
            ]);
    }

    private function getClientNewOrder(Order $order, Content $content): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->subject("Commande N°{$order->getInitialZeroId()}: Reçue")
            ->to($order->getClient()->getAddress())
            ->htmlTemplate('email/order/new_client.html.twig')
            ->context([
                'content' => $content,
                'order' => $order
            ]);
    }

}