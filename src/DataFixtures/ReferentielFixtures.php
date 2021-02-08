<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Referentiel;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReferentielFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $referentiel_libelles = ["Developpeur Web", "Referant digital", "Data Analyst"];
        for($i=0;$i<count($referentiel_libelles);$i++){
            $referentiel = new Referentiel;
            $referentiel->setLibelle($faker->unique()->randomElement($referentiel_libelles))
                        ->setPresentation($faker->text(20))
                        ->setCritereAdmission($faker->text(20))
                        ->setCritereEvaluation($faker->text(20))
                        ->addGroupeCompetence($this->getReference('gc'.$i));
            $manager->persist($referentiel);
            $this->addReference('referentiel'.$i, $referentiel);
        }
            $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            GroupeCompetencesFixtures::class
        );
        
    }
}
