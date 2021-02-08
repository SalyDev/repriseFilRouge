<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Repository\FormateurRepository;
use App\Services\UploadAvatarService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("api/admin/formateurs", name="")
*/
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
     * @Route("", methods="POST", name="addFormateur",  defaults={"_api_collection_operation_name"="addFormateur"})
     */
    public function addFormateur(Request $request){
        $this->denyAccessUnlessGranted('ADD', new formateur, "Accès non autorisé");
        $formateur = new Formateur();
        $this->uploadAvatarService->giveRole("formateur", $formateur);
        return $this->userService->addUser($request, $formateur, "Formateur ajouté avec succès");
    }

    /**
     * @Route("", name="showFormateurs", methods="GET", defaults={"_api_collection_operation_name"="showFormateurs"})
     */
    public function showFormateurs()
    {
        $this->denyAccessUnlessGranted('VIEW_ALL', new Formateur, "Accès non autorisé");
        return $this->userService->showUsers($this->formateurRepository->findBy(["archive" => false]));
    }

    /**
     * @Route("/{id}", name="updateFormateur", methods="PUT", defaults={"_api_item_operation_name"="updateFormateur"})
     */
    public function updateFormateur(int $id, Request $request)
    {
        $formateur = $this->formateurRepository->findOneBy(["id" => $id]);
        $this->denyAccessUnlessGranted('EDIT', $formateur, "Accès refusé");
        return $this->userService->updateUser($formateur, $request, "Formateur inexistant", "Formateur modifié avec succes");
    }

     /**
     * @Route("/{id}", name="showOneFormateur", methods="GET", defaults={"_api_item_operation_name"="showOneFormateur"})
     */
    public function showOneFormateur(int $id){
        $formateur = $this->formateurRepository->findOneBy(["id" => $id]);
        $this->denyAccessUnlessGranted('EDIT', $formateur, "Accès non autorisé");
        return $this->userService->showOneUser($id, $this->formateurRepository);
    }
}
