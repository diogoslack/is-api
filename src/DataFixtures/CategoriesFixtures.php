<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categoriesData = ['Category 1', 'Category 2'];

        foreach ($categoriesData as $categoryName) {
            $category = new Categories();
            $category->setName($categoryName);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
