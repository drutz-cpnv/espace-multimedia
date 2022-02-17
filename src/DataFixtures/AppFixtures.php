<?php

namespace App\DataFixtures;

use App\Entity\Content;
use App\Entity\Paragraph;
use App\Entity\Section;
use App\Entity\State;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {

        //$drutz = $this->userRepository->findOneBy(['email', 'dimitri.rutz@cpnv.ch']);

        $toPersist = [];

        // Création des status des commandes
        /*$toPersist[] = (new State())->setName("En attente")
            ->setSlug("pendding")
            ->setColor("ff7b1e");

        $toPersist[] = (new State())->setName("Acceptée")
            ->setSlug("accepted")
            ->setColor("38ce35");

        $toPersist[] = (new State())->setName("Refusée")
            ->setSlug("refused")
            ->setColor("e01313");

        $toPersist[] = (new State())->setName("En retard")
            ->setSlug("late")
            ->setColor("ce7535");

        $toPersist[] = (new State())->setName("Erreur")
            ->setSlug("error")
            ->setColor("ff0000");

        $toPersist[] = (new State())->setName("Terminée")
            ->setSlug("termianted")
            ->setColor("1c6fe1");

        $toPersist[] = (new State())->setName("Annulée")
            ->setSlug("cancelled")
            ->setColor("353b48");*/



        /*$content = (new Content())
            ->setName("Contenu de la page \"Informations\"")
            ->setCreatedBy($drutz)
            ->setUpdatedBy($drutz);

        $section = (new Section())
            ->setName("TODO: Remplir le contenu de cette page")
            ->setContent($content);

        $paragraph = (new Paragraph())
            ->setName("Paragraphe 1")
            ->setText("TODO: Remplir le premier paragraphe")
            ->setSection($section);

        $toPersist[] = $content;
        $toPersist[] = $section;
        $toPersist[] = $paragraph;*/

        foreach ($toPersist as $item) {
            $manager->persist($item);
        }

        $manager->flush();
    }
}
