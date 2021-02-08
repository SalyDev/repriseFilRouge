<?php
namespace App\Services;

use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Request;

class UploadAvatarService{

    private $profilRepository;

    function __construct(ProfilRepository $profilRepository)
    {
        $this->profilRepository = $profilRepository;
    }

    public function uploadAvatar(Request $request, $cle){
        $avatar = $request->files->get($cle);
        $avatarbin = fopen($avatar, 'rb');
        return $avatarbin;
    }

    public function giveRole($libelle, $userObject){
        $profilObject = $this->profilRepository->findOneBy(["libelle" => $libelle]);
        $userObject->setProfil($profilObject);
        $roles[] = 'ROLE_'.strtoupper($profilObject->getLibelle());
        $userObject->setRoles($roles);
    }
}