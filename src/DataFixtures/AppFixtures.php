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
    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
    }
}
