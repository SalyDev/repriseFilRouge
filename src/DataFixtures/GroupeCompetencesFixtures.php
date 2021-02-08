<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\GroupeCompetences;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GroupeCompetencesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for($i=0;$i<3;$i++){
            $gc = new GroupeCompetences;
            $gc->setLibelle($faker->text(10));
            $gc->setDescriptif($faker->text(20));
            $this->addReference('gc'.$i, $gc);
            $gc->addCompetence($this->getReference('competence'.$i));
            $manager->persist($gc);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CompetenceFixtures::class
        );
    }
}
