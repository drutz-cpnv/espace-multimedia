<?php

namespace App\Maker;

use App\Entity\User;
use App\Services\ContentManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @method string getCommandDescription()
 */
class ContentMake extends AbstractMaker
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContentManager $contentManager
    )
    {
    }

    /**
     * @inheritDoc
     */
    public static function getCommandName(): string
    {
        return "make:content";
    }

    /**
     * @inheritDoc
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument('content', InputArgument::OPTIONAL, 'The name of the content (e.g. <fg=yellow>Email — New order</>)')
            ->addArgument('content_key', InputArgument::OPTIONAL, 'Key of the content entity (e.g. <fg=yellow>email.order.new</>)')
        ;
    }


    /**
     * @inheritDoc
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {

        $drutz = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'dimitri.rutz@cpnv.ch']);

        $content = $this->contentManager->arrayToContent(new ArrayCollection([
            $input->getArgument('content') => [
                'Première Section' => [
                    'Premier paragraphe' => "À remplire",
                    'Second paragraphe' => "À remplire",
                ],
                'Seconde Section' => [
                    'Premier paragraphe' => "À remplire",
                    'Second paragraphe' => "À remplire",
                ],
            ],
            $input->getArgument('content_key')
        ]));

        $content->setCreatedBy($drutz);
        $content->setUpdatedBy($drutz);

        $this->entityManager->persist($content);
        $this->entityManager->flush();

        $this->writeSuccessMessage($io);
        $io->text([
            'The content have been created',
        ]);
    }



    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method string getCommandDescription()
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }
}