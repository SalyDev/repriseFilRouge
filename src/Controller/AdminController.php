<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Services\UserService;
use App\Repository\AdminRepository;
use App\Services\UploadAvatarService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("api/admin/users/admins", name="")
*/
class AdminController extends AbstractController
{
    private $userService, $adminRepository, $uploadAvatarService;
    function __construct(AdminRepository $adminRepository, UserService $userService, UploadAvatarService $uploadAvatarService)
    {
       $this->adminRepository = $adminRepository; 
       $this->userService = $userService;
       $this->uploadAvatarService = $uploadAvatarService;
    }
/**
     * @Route("", name="addAmin", methods="POST", defaults={"_api_collection_operation_name"="addAmin"})
     */
    public function addAmin(Request $request)
    {
        $admin = new Admin();
        $this->uploadAvatarService->giveRole("admin", $admin);
        return $this->userService->addUser($request, $admin, "Admin ajouté avec succès");
    }

    /**
     * @Route("", name="showAdmins", methods="GET", defaults={"_api_collection_operation_name"="showAdmins"})
     */
    public function showAdmins()
    {
        return $this->userService->showUsers($this->adminRepository);
    }

   /**
     * @Route("/{id}", name="updateAdmin", methods="PUT", defaults={"_api_item_operation_name"="updateAdmin"})
     */
    public function updateAdmin(int $id, Request $request){
        $object = $this->adminRepository->findOneBy(["id" => $id]);
        return $this->userService->updateUser($object, $request, "Admin inexistant", "Admin modifié avec succès");
    }

    /**
     * @Route("/{id}", name="showOneAdmin", methods="GET", defaults={"_api_item_operation_name"="showOneAdmin"})
     */
    public function showOneAdmin(int $id){
        return $this->userService->showOneUser($id, $this->adminRepository);
    }
   
}
