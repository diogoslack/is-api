<?php

namespace App\DataFixtures;

use App\Entity\Sectors;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SectorsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sectorsData = ['North', 'South', 'West', 'East'];

        foreach ($sectorsData as $sectorName) {
            $sector = new Sectors();
            $sector->setName($sectorName);
            $manager->persist($sector);
        }

        $manager->flush();
    }
}
