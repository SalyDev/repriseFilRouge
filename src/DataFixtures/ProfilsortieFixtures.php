<?php

namespace App\DataFixtures;

use App\Entity\Profilsortie;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfilsortieFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        $faker = Faker\Factory::create();
        $profilsorties =["Developpeur CMS", "Fronted Developper", "Backend Developper"];
        for($i=0;$i<count($profilsorties);$i++){
            $profilsortie = new Profilsortie;
            $profilsortie->setLibelle($faker->unique()->randomElement($profilsorties));
            $this->addReference('ps'.$i, $profilsortie);
            $manager->persist($profilsortie);
        }
        $manager->flush();
    }
}
