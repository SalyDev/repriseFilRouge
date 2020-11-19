<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Entity\User;
use App\Repository\ApprenantRepository;
use App\Services\MyService;
use App\Services\UploadAvatarService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApprenantController extends AbstractController
{

    private $uploadAvatarService, $userService, $apprenantRepository;

    function __construct(ApprenantRepository $apprenantRepository, UploadAvatarService $uploadAvatarService, UserService $userService){
        $this->userService = $userService;
        $this->uploadAvatarService = $uploadAvatarService;
        $this->apprenantRepository = $apprenantRepository;
    }
    /**
     * @Route("api/admin/users/apprenants", name="addApprenant", methods="POST", defaults={"_api_item_operation_name"="addApprenant"})
     */
    public function addApprenant(Request $request)
    {
        $donnees=$request->request;
        $apprenant = new Apprenant();
        $apprenant->setAdresse($donnees->get("adresse"));
        $apprenant->setTelephone($donnees->get("telephone"));
        $this->uploadAvatarService->giveRole("apprenant", $apprenant);
        return $this->userService->addUser($request, $apprenant, "Apprenant ajouté avec succès");
    }

    /**
     * @Route("api/admin/users/apprenants", name="showApprenants", methods="GET", defaults={"_api_collection_operation_name"="showApprenants"})
     */
    public function showApprenants()
    {
        return $this->userService->showUsers($this->apprenantRepository);
    }

    /**
     * @Route("api/admin/users/apprenants/{id}", name="updateApprenant", methods="PUT")
     */
    public function updateApprenant(int $id, Request $request)
    {
        $apprenant = $this->apprenantRepository->findOneBy(["id" => $id]);
        return $this->userService->updateUser($apprenant, $request);
    }


}
