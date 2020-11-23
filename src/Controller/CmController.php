<?php

namespace App\Controller;

use App\Entity\CM;
use App\Repository\CMRepository;
use App\Services\UserService;
use App\Services\UploadAvatarService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("api/admin/users/cm", name="")
*/
class CmController extends AbstractController
{
    private $uploadAvatarService, $cmRepository, $userService;
    function __construct(UploadAvatarService $uploadAvatarService, CMRepository $cmRepository, UserService $userService)
    {
        $this->uploadAvatarService = $uploadAvatarService;
        $this->cmRepository = $cmRepository;
        $this->userService = $userService;
    }
    /**
     * @Route("", methods="POST", name="addCm",  defaults={"_api_collection_operation_name"="addCm"})
     */
    public function addCm(Request $request){
        $cm = new CM();
        $this->uploadAvatarService->giveRole("cm", $cm);
        return $this->userService->addUser($request, $cm, "CM ajouté avec succès");
    }

    /**
     * @Route("", name="showCm", methods="GET", defaults={"_api_collection_operation_name"="showCm"})
     */
    public function showCm()
    {
        return $this->userService->showUsers($this->cmRepository);
    }

    /**
     * @Route("/{id}", name="updateCm", methods="PUT", defaults={"_api_item_operation_name"="updateCm"})
     */
    public function updateCm(int $id, Request $request)
    {
        $cm = $this->cmRepository->findOneBy(["id" => $id]);
        return $this->userService->updateUser($cm, $request, "CM inexistant", "CM modifié avec succes");
    }

     /**
     * @Route("/{id}", name="showOneCm", methods="GET", defaults={"_api_item_operation_name"="showOneCm"})
     */
    public function showOneCm(int $id){
        return $this->userService->showOneUser($id, $this->cmRepository);
    }
}
