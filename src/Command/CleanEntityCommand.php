<?php

namespace App\Command;

use App\Services\SetupService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:entity:clean',
    description: 'Clean an entity',
    hidden: false
)]
class CleanEntityCommand extends Command
{

    protected static $defaultName = 'app:entity:clean';

    public function __construct(private LoggerInterface $logger, private EntityManagerInterface $entityManager)
    {
        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Command to import users from an JSON file !')
            ->addArgument('entity', InputArgument::REQUIRED, "Entity name")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('Cleaning entity');
        $entity = $input->getArgument('entity');

        if (class_exists($entity)) {

            $repo = $this->entityManager->getRepository($entity);
            $deleted = [];

            $content = $repo->findAll();

            foreach ($content as $item) {
                $deleted[get_class($item)] = $item->getId();
                $this->entityManager->remove($item);
            }

            $this->entityManager->flush();

            $output->write($deleted);
            return Command::SUCCESS;
        }

        $output->write("La commande à échoué");
        return Command::FAILURE;

    }

}