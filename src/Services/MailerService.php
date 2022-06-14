<?php

namespace App\Services;

use App\Repository\SettingsRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{

    private Address $sender;

    public function __construct(
        private SettingsRepository $settingsRepository,
        private MailerInterface $mailer
    )
    {
        $this->sender = Address::create('Espace Multimédia <no-reply@multimedia.mediamatique.ch>');
    }

    public function send(TemplatedEmail $email)
    {
        $setting = $this->settingsRepository->findOneByKey('email.send');
        if (is_null($setting)) return false;
        if(filter_var($setting->getValue(), FILTER_VALIDATE_BOOLEAN)) {
            $email->from($this->sender);
            $this->mailer->send($email);
            return true;
        }
        return false;
    }

}