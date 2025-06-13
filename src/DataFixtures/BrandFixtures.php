<?php

namespace App\DataFixtures;

use App\Entity\Brand\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brands = [
            ['MTN Cameroon', 'https://img.co/logo.png', 92],
            ['Orange Cameroon', 'https://img.co/logo.png', 89],
            ['Brasseries du Cameroun', 'https://img.co/logo.png', 88],
            ['Dangote Group', 'https://img.co/logo.png', 94],
            ['MTN Nigeria', 'https://img.co/logo.png', 91],
            ['Zenith Bank', 'https://img.co/logo.png', 92],
            ['Total France', 'https://img.co/logo.png', 88],
            ['Orange France', 'https://img.co/logo.png', 90],
            ['BNP Paribas', 'https://img.co/logo.png', 89],
            ['Apple Inc', 'https://img.co/logo.png', 95],
            ['Microsoft', 'https://img.co/logo.png', 93],
            ['Google', 'https://img.co/logo.png', 94],
        ];

        foreach ($brands as [$name, $image, $rating]) {
            $brand = new Brand();
            $brand->setBrandName($name)
                  ->setBrandImage($image)
                  ->setRating($rating);

            $manager->persist($brand);
        }

        $manager->flush();
    }
}
