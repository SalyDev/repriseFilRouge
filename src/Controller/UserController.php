<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Repository\ApprenantRepository;
use App\Services\MyService;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use App\Repository\ProfilsortieRepository;
use App\Services\UploadAvatarService;
use App\Services\UserService;

;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $uploadAvatarService, $userRepository, $userService, $profilRepository, $profilsortieRepository, $apprenantRepository;
    
    function __construct(UserService $userService, UploadAvatarService $uploadAvatarService, UserRepository $userRepository, ProfilRepository $profilRepository, ProfilsortieRepository $profilsortieRepository, ApprenantRepository $apprenantRepository)
    {
        $this->uploadAvatarService = $uploadAvatarService;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->profilRepository = $profilRepository;
        $this->profilsortieRepository = $profilsortieRepository;
        $this->apprenantRepository = $apprenantRepository;
    }

    // fonction permettant d'ajouter un user


    // fonction permettant de modifier un user


    // fonction permettant de recuperer la liste des utilisateurs
     /**
     * @Route("api/admin/users", name="showUsers", methods="GET", defaults={"_api_collection_operation_name"="showUsers"})
     */
    public function showAdmins()
    {
        $this->denyAccessUnlessGranted('ADD', new Admin, "Accès refusé");
        return $this->userService->showUsers($this->userRepository->findBy(["archive" => false]));
    }


    // fonction permettant de recuperer un user par son email
       /**
     * @Route("api/admin/user/{email}", name="getUserByEmail", methods="GET", defaults={"_api_collection_operation_name"="getUserByEmail"})
     */

     public function getUserByEmail(string $email){
        $user = $this->userRepository->findOneBy([
            "email" => $email,
            "archive" => false
            ]);
            return $this->json($user, 200, []);
     }

    //  la liste des utilisateurs qui ont le mm profil
      /**
     * @Route("api/admin/profils/{id}/users", name="getUserOfProfil", methods="GET", defaults={"_api_collection_operation_name"="getUserOfProfil"})
     */
     public function getUserOfProfil(int $id){
        $profil = $this->profilRepository->findOneBy(["id" => $id]);
        $users = $this->userRepository->findBy([
            "profil" => $profil,
            "archive" => false
            ]);
        return $this->json($users, 200, []);
     }

    // la liste des apprenants qui le mm profil de sorties
       /**
     * @Route("api/admin/profilsorties/{id}/apprenants", name="getUserOfPs", methods="GET", defaults={"_api_collection_operation_name"="getUserOfPs"})
     */
    public function getUserOfPs(int $id){
        $ps = $this->profilsortieRepository->findBy(["id" => $id]);
        $users = $this->apprenantRepository->findBy([
            "profilsortie" => $ps,
            "archive" => false
            ]);
        return $this->json($users, 200, []);
     }

     

}
