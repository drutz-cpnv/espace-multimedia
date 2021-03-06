<?php

namespace App\Services;

use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Security;

class SettingsAccessCheckerService
{

    public function __construct(private readonly SettingsRepository $settingsRepository, private readonly Security $security)
    {
    }

    public function checkAccess(string $settingKey): bool
    {
        $access = $this->settingsRepository->findOneByKey($settingKey);
        if(is_null($access)){
            return false;
        }
        return filter_var($access->getValue(), FILTER_VALIDATE_BOOLEAN);
    }

    public function access(string $key): bool
    {
        if($this->checkAccess($key)) return true;
        if($this->security->isGranted('ROLE_ADMIN')) return true;
        return false;
    }
}