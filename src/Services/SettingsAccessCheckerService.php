<?php

namespace App\Services;

use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class SettingsAccessCheckerService
{

    public function __construct(private SettingsRepository $settingsRepository)
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

    public function access(string $key, FlashBagInterface $flashBag): bool
    {
        if($this->checkAccess($key)) return true;
        $flashBag->add('error', "L'accès à cette fonctionnalité a été restreint temporairement.");
        return false;
    }
}