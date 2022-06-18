<?php

namespace App\Command;

use App\Services\SetupService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupRoomCommand extends Command
{
    protected static $defaultName = 'app:setup:room';

    public function __construct(private LoggerInterface $logger, private SetupService $setupService)
    {
        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Install default room equipments !');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('App setup command running');
        $this->setupService->setupRoomEquipment();
        return Command::SUCCESS;
    }
}