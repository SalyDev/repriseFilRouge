<?php

namespace App\Controller;

use App\Repository\ProfilRepository;
use App\Services\ArchivageService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    private $archivageService;
    private $profilRepository;

    function __construct(ArchivageService $archivageService, ProfilRepository $profilRepository)
    {
        $this->archivageService = $archivageService;
        $this->profilRepository = $profilRepository;
    }
    /**
     * @Route("/api/admin/profils/{id}", name="archiveProfil", methods="DELETE", defaults={"_api_item_operation_name"="archiveProfil"})
     */
    public function archiveProfil(int $id){
        return $this->archivageService->archiver($id, $this->profilRepository);
    }

    /**
     * @Route("/api/admin/profils", name="listeOfProfilsNoArchives", methods="GET", defaults={"_api_collection_operation_name"="listeOfProfilsNoArchives"})
     */
    public function showProfilsNoArchives(){
        return $this->archivageService->getElementsNoArchives($this->profilRepository);
    }
}
