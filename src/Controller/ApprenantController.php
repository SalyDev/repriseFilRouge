<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Repository\ApprenantRepository;
use App\Services\UploadAvatarService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Admin;

/**
* @Route("api/admin/apprenants", name="")
*/
class ApprenantController extends AbstractController
{

    private $uploadAvatarService, $userService, $apprenantRepository;

    function __construct(ApprenantRepository $apprenantRepository, UploadAvatarService $uploadAvatarService, UserService $userService){
        $this->userService = $userService;
        $this->uploadAvatarService = $uploadAvatarService;
        $this->apprenantRepository = $apprenantRepository;
    }
    /**
     * @Route("", name="addApprenant", methods="POST", defaults={"_api_collection_operation_name"="addApprenant"})
     */
    public function addApprenant(Request $request)
    {
        $this->denyAccessUnlessGranted('ADD', new Apprenant, "Accès non autorisé");
        $apprenant = new Apprenant();
        $this->uploadAvatarService->giveRole("apprenant", $apprenant);
        return $this->userService->addUser($request, $apprenant, "Apprenant ajouté avec succès");
    }

    /**
     * @Route("", name="showApprenants", methods="GET", defaults={"_api_collection_operation_name"="showApprenants"})
     */
    public function showApprenants()
    {
        $this->denyAccessUnlessGranted('VIEW_ALL', new Apprenant, "Accès non autorisé");
        return $this->userService->showUsers( $this->apprenantRepository->findBy(["archive" => false]));
    }

    /**
     * @Route(
     * name="updateApprenant",
     * path="/{id}", 
     * methods={"PUT"}, 
     * defaults={
     * "_controller"="app\Controller\ApprenantController::updateApprenant",
     * "_api_item_operation_name"="updateApprenant", 
     * "_api_resource_classe"=Apprenant::class})
     */
    public function updateApprenant(int $id, Request $request){
        $object = $this->apprenantRepository->findOneBy(["id" => $id]);      
        $this->denyAccessUnlessGranted('EDIT', $object, "Accès est refusé");
        return $this->userService->updateUser($object, $request, "Apprenant inexistant", "Apprenant modifié avec succès");
    }

    /**
     * @Route("/{id}", name="showOneApprenant", methods="GET", defaults={"_api_item_operation_name"="showOneApprenant"})
     */
    public function showOneApprenant(int $id){
        $apprenant = $this->apprenantRepository->findOneBy(["id" => $id]);
        $this->denyAccessUnlessGranted('EDIT', $apprenant, "Accès non autorisé"); 
        return $this->userService->showOneUser($id, $this->apprenantRepository);
    }

  
   


}