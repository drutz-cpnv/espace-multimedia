<?php

namespace App\Command;

use App\Services\SetupService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportUsersCommand extends Command
{

    protected static $defaultName = 'app:import:users';

    public function __construct(private LoggerInterface $logger, private SetupService $setupService)
    {
        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Command to import users from an JSON file !');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('Importing users');
        $result = $this->setupService->importUsers();

        if(!$result) {
            $output->write("La commande à échoué");
            return Command::FAILURE;
        }

        $output->write($result);
        return Command::SUCCESS;
    }

}