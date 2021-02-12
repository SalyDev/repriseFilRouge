<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Apprenant;
use App\Entity\Admin;
use App\Entity\CM;
use App\Entity\Formateur;
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
            $profil = $this->profilRepository->findOneBy(["id" => $this->getReference($i)]);
            $roles[$i][] = 'ROLE_'.strtoupper($profil->getLibelle());
            for($j=0; $j<3; $j++){
            switch ($profil->getLibelle()) {
                case 'apprenant':
                    $user = new Apprenant();
                    $user->setProfilsortie($this->getReference('ps'.$j));
                    $user->setAvatar($faker->imageUrl(640, 480, 'people'));
                    break;
                case 'admin':
                    $user = new Admin();
                    break;
                case 'formateur':
                    $user = new Formateur();
                    break;
                case 'CM':
                    $user = new CM();
                    break;
                default:
                    break;
            }
            $user->setNom($faker->lastName());
            $user->setPrenom($faker->firstName());
            $user->setEmail("mail".$i.$j."@gmail.com");
            $pass = $this->encoder->encodePassword($user, "password");
            $user->setPassword($pass);
            // referencement vers les profil fixtures
            $user->setProfil($this->getReference($i));
            //roles
            $user->setRoles($roles[$i]);
            //images utilisés pour les avatar
            // $usersAvatar = [
            //     'image1.jpeg',
            //     'image2.jpeg',
            //     'image3.png',
            // ];
            // $avatar = $faker->randomElement($usersAvatar);
            // $user->setAvatar($avatar);

            $manager->persist($user);
        }
    }
        $manager->flush();
    }
}
