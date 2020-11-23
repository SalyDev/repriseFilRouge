<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProfilFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $profilNames= ["admin", "CM", "apprenant", "formateur"];
        for($i=0;$i<4;$i++) {
            $new_profil = new Profil();
            $new_profil->setLibelle($profilNames[$i]);
            $new_profil->setArchive(false);
            $manager->persist($new_profil);

            //on definit le referencement qu'on va utiliser ensuite dans userFixtures 
            //pointer ici
            $this->addReference($i, $new_profil);
        }
        $manager->flush();
    }
}
