<?php

namespace App\Command;

use App\Repository\OrderRepository;
use App\Services\SettingsService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderResetCommand extends Command
{

    protected static $defaultName = 'app:order:reset';

    public function __construct(private LoggerInterface $logger, private SettingsService $settingsService)
    {
        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Reset order table.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('Reset orders');
        $this->settingsService->resetOrders();
        return Command::SUCCESS;
    }


}