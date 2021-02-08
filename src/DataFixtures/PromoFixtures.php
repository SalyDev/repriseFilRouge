<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Promo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PromoFixtures extends Fixture implements DependentFixtureInterface 
{
    
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $programmes = [
            "file1.pdf",
            "file2.pdf",
            "file3.pdf"
        ];

        $avatars = [
            'image1.jpeg',
            'image2.jpeg',
            'image3.png',
        ];

        for($i=0;$i<count($avatars);$i++){
            $promo = new Promo;
            $promo->setReferenceagate($faker->text(5))
                    ->setDescription($faker->text(5))
                    ->setTitre($faker->unique()->title())
                    ->setProgramme($faker->randomElement($programmes))
                    ->setAvatar($faker->randomElement($avatars))
                    ->setLieu($faker->city)
                    ->setReferentiel($this->getReference('referentiel'.$i));
            $manager->persist($promo);
            $this->addReference('promo'.$i, $promo);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ReferentielFixtures::class
        );
    }
}
