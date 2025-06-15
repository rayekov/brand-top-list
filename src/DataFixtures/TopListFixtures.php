<?php

namespace App\DataFixtures;

use App\Entity\Brand\Brand;
use App\Entity\Country\Country;
use App\Entity\TopList\TopListEntry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TopListFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brandRepo = $manager->getRepository(Brand::class);
        $countryRepo = $manager->getRepository(Country::class);

        $brands = $brandRepo->findAll();

        $cameroon = $countryRepo->findOneBy(['isoCode' => 'CM']);
        $nigeria = $countryRepo->findOneBy(['isoCode' => 'NG']);
        $france = $countryRepo->findOneBy(['isoCode' => 'FR']);
        $usa = $countryRepo->findOneBy(['isoCode' => 'US']);

        $toplists = [
            [$cameroon, [$brands[0], $brands[1], $brands[2]]],
            [$nigeria, [$brands[3], $brands[4], $brands[5]]],
            [$france, [$brands[6], $brands[7], $brands[8]]],
            [$usa, [$brands[9], $brands[10], $brands[11]]],
        ];

        foreach ($toplists as [$country, $countryBrands]) {
            if ($country) {
                $position = 1;
                foreach ($countryBrands as $brand) {
                    if ($brand) {
                        $entry = new TopListEntry();
                        $entry->setCountry($country)
                              ->setBrand($brand)
                              ->setPosition($position)
                              ->setIsActive(true);

                        $manager->persist($entry);
                        $position++;
                    }
                }
            }
        }

        $manager->flush();
    }
}
