<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Groupe;
use App\Repository\ApprenantRepository;
use App\Repository\FormateurRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GroupeFixtures extends Fixture implements DependentFixtureInterface
{
    private $apprenantRepository, $formateurRepository;
    function __construct(ApprenantRepository $apprenantRepository, FormateurRepository $formateurRepository)
    {
        $this->apprenantRepository = $apprenantRepository;
        $this->formateurRepository = $formateurRepository;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for($i=0;$i<3;$i++){
            $groupe = new Groupe();
            $groupe->setNom($faker->text(10));
            $groupe->setPromo($this->getReference('promo'.$i));
            $apprenants = $this->apprenantRepository->findAll();
            $formateurs = $this->formateurRepository->findAll();
            for($j=0;$j<count($apprenants);$j++){
                $groupe->addApprenant($apprenants[$j]);
                $groupe->addFormateur($formateurs[$j]);
            }
            $manager->persist($groupe);
            $this->addReference('groupe'.$i, $groupe);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            GroupeCompetencesFixtures::class,
            PromoFixtures::class
        );
    }
}
