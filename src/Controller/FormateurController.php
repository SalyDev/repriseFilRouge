<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Repository\FormateurRepository;
use App\Services\UploadAvatarService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    private $uploadAvatarService, $formateurRepository, $userService;
    function __construct(UploadAvatarService $uploadAvatarService, FormateurRepository $formateurRepository, UserService $userService)
    {
        $this->uploadAvatarService = $uploadAvatarService;
        $this->formateurRepository = $formateurRepository;
        $this->userService = $userService;
    }
    /**
     * @Route("api/admin/users/formateurs", methods="POST", name="addFormateur",  defaults={"_api_item_operation_name"="addFormateur"})
     */
    public function addFormateur(Request $request){
        $formateur = new Formateur();
        $this->uploadAvatarService->giveRole("formateur", $formateur);
        return $this->userService->addUser($request, $formateur, "Formateur ajouté avec succès");
    }

    /**
     * @Route("api/admin/users/formateurs", name="showFormateurs", methods="GET", defaults={"_api_item_operation_name"="showFormateurs"})
     */
    public function showFormateurs()
    {
        return $this->userService->showUsers($this->formateurRepository);
    }
}
