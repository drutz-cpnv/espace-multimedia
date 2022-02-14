<?php

namespace App\DataFixtures;

use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $toPersist = [];

        /**
         * Création des status des commandes
         */
        /*$toPersist[] = (new State())->setName("En attente")
            ->setSlug("pendding")
            ->setColor("ff7b1e");

        $toPersist[] = (new State())->setName("Acceptée")
            ->setSlug("accepted")
            ->setColor("38ce35");

        $toPersist[] = (new State())->setName("Refusée")
            ->setSlug("refused")
            ->setColor("e01313");

        $toPersist[] = (new State())->setName("En attente du matériel")
            ->setSlug("equipment_pending")
            ->setColor("ce7535");

        $toPersist[] = (new State())->setName("Matériel incomplet")
            ->setSlug("equipment_invalid")
            ->setColor("ff0000");

        $toPersist[] = (new State())->setName("Passée")
            ->setSlug("passed")
            ->setColor("1c6fe1");*/
        $toPersist[] = (new State())->setName("Annulée")
            ->setSlug("cancelled")
            ->setColor("353b48");



        foreach ($toPersist as $item) {
            $manager->persist($item);
        }

        $manager->flush();
    }
}
