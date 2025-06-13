<?php

namespace App\DataFixtures;

use App\Entity\Country\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $countries = [
            ['CM', 'Cameroon', true],
            ['NG', 'Nigeria', false],
            ['FR', 'France', false],
            ['US', 'United States', false],
        ];

        foreach ($countries as [$isoCode, $name, $isDefault]) {
            $country = new Country();
            $country->setIsoCode($isoCode)
                   ->setName($name)
                   ->setIsDefault($isDefault);
            
            $manager->persist($country);
            
        }

        $manager->flush();
    }
}
