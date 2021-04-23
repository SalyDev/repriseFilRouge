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
* @Route("api/admin/cms", name="")
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
        $this->denyAccessUnlessGranted('ADD', new CM, "Accès non autorisé");
        $cm = new CM();
        $this->uploadAvatarService->giveRole("cm", $cm);
        return $this->userService->addUser($request, $cm, "CM ajouté avec succès");
    }

    /**
     * @Route("", methods="GET", name="showCm", defaults={"_api_collection_operation_name"="showCm"})
     */
    public function showCm()
    {
        $this->denyAccessUnlessGranted('VIEW_ALL', new CM, "Accès non autorisé");
        return $this->userService->showUsers($this->cmRepository->findBy(["archive" => false]));
    }

    /**
     * @Route("/{id}", name="updateCm", methods="PUT", defaults={"_api_item_operation_name"="updateCm"})
     */
    public function updateCm(int $id, Request $request)
    {
        $cm = $this->cmRepository->findOneBy(["id" => $id]);
        $this->denyAccessUnlessGranted('EDIT', $cm, "Accès non autorisé");      
        $cm = $this->userService->updateUser($cm, $request, "CM inexistant");
        return $this->json($cm, 201);
    }

     /**
     * @Route("/{id}", name="showOneCm", methods="GET", defaults={"_api_item_operation_name"="showOneCm"})
     */
    public function showOneCm(int $id){
        $cm = $this->cmRepository->findOneBy(["id" => $id]);
        $this->denyAccessUnlessGranted('EDIT', $cm, "Accès non autorisé");
        return $this->userService->showOneUser($id, $this->cmRepository);
    }
}
