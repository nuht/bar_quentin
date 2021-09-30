<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Faker;
use App\Entity\Beer;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // mysql -u root -p pour se connecter avec le mdp

        $faker = Faker\Factory::create('fr_FR');

        // Categories normals
        $categoriesNormals = ['blonde', 'brune', 'blonde'];
        foreach($categoriesNormals as $categoryNormal) {
            $category = new Category();
            $category->setName($categoryNormal);
            $manager->persist($category);
        }

        // Categories special
        $categoriesSpecials = ['houblon', 'rose', 'menthe', 'grenadine', 'réglisse', 'marron', 'whisky', 'bio'];
        foreach($categoriesSpecials as $categorySpecial) {
            $category = new Category();
            $category->setName($categorySpecial);
            $category->setTerm('special');
            $manager->persist($category);
        }

        $manager->flush();

        $categoriesNormals = $manager->getRepository(Category::class)->findByTerm('normal');
        $categoriesSpecials = $manager->getRepository(Category::class)->findByTerm('special');

        $countries = ['belgium', 'french', 'england', 'germany'];
        foreach($countries as $country) {
            $countryElement = new Country();
            $countryElement->setName($country);
            $countryElement->setEmail($faker->email);
            $countryElement->setAddress($faker->address);
            for($b = rand(2,5); $b < 10; $b++) {
                $beer = new Beer();
                $beer->setName($faker->word);
                $beer->setPublishedAt($faker->dateTime);
                $beer->setDescription($faker->text);
                $beer->setCountry($countryElement);
                $beer->setPrice($faker->randomFloat(2, 4, 30));
                $category = $categoriesNormals[array_rand($categoriesNormals)];
                $category->addBeer($beer);
                $manager->persist($category);

                $nbCategoriesSpecial = rand(1,2);
                while($nbCategoriesSpecial > 0) {
                    $category = $categoriesSpecials[array_rand($categoriesSpecials)];
                    $category->addBeer($beer);
                    $manager->persist($category);
                    $nbCategoriesSpecial--;
                }
                $manager->persist($beer);
            }
            $manager->persist($countryElement);
        }

        $manager->flush();
    }
}
