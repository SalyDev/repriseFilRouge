<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Competence;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CompetenceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for($i=0;$i<=3;$i++){
            $competence = new Competence;
            $competence->setLibelle($faker->domainName);
            $competence->setDescriptif($faker->text(50));
            $this->addReference('competence'.$i, $competence);
            for($j=1;$j<=3;$j++){
                $l = 3*$i+$j;
                $competence->addNiveau($this->getReference('niveau'.$l));
            }
            $manager->persist($competence);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            NiveauFixtures::class
        );
    }
}
