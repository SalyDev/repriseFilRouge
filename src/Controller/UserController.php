<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Services\MyService;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
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
    private $uploadAvatarService, $userRepository, $userService;
    
    function __construct(UserService $userService, UploadAvatarService $uploadAvatarService, UserRepository $userRepository)
    {
        $this->uploadAvatarService = $uploadAvatarService;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
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
     

}
