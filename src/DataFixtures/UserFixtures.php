<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\User;
use App\Repository\ProfilRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $encoder;
    private $profilRepository;
    public function __construct(UserPasswordEncoderInterface $encoder, ProfilRepository $profilRepository)
    {
        $this->encoder = $encoder;
        $this->profilRepository = $profilRepository;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 4; $i++) {
            $user = new User();
            $user->setNom($faker->lastName());
            $user->setPrenom($faker->firstName());
            $user->setEmail($faker->email());
            $pass = $this->encoder->encodePassword($user, "password");
            $user->setPassword($pass);
            $user->setProfil($this->getReference($i));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
