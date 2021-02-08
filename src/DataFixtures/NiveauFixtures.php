<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Niveau;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class NiveauFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for($i=1;$i<=12;$i++){
            $niveau = new Niveau();
            $niveau->setCritereEvaluation($faker->text(20));
            $niveau->setActions($faker->text(20));
            $manager->persist($niveau);
            $this->addReference('niveau'.$i, $niveau);
            
        }
        $manager->flush();
    }
   
}
