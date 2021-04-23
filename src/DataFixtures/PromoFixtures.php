<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Promo;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PromoFixtures extends Fixture implements DependentFixtureInterface 
{
    
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for($i=0;$i<3;$i++){
            $promo = new Promo;
            $img = file_get_contents('https://source.unsplash.com/1080x720/?person');
            $promo->setReferenceagate($faker->text(5))
                    ->setDescription($faker->text(5))
                    ->setTitre($faker->unique()->title())
                    ->setAvatar($img)
                    ->setLieu($faker->city)
                    ->setReferentiel($this->getReference('referentiel'.$i))
                    ->setLangue('Français')
                    ->setDebut(date_format(new \DateTime(),"Y/m/d H:i:s"))
                    ->setFin(date_format(new \DateTime(),"Y/m/d H:i:s"))
                    ;
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
