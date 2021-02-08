<?php

namespace App\Controller;

use App\Repository\GroupeRepository;
use App\Repository\ApprenantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupeController extends AbstractController
{

    private $groupeRepository, $apprenantRepository, $manager;
    function __construct(GroupeRepository $groupeRepository, ApprenantRepository $apprenantRepository, EntityManagerInterface $manager)
    {
        $this->groupeRepository = $groupeRepository;
        $this->apprenantRepository = $apprenantRepository;
        $this->manager = $manager;
    }
    /**
     * @Security("is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR')", message="Accès refusé")
     * @Route(path="api/admin/groupes/{idGroup}/apprenants/{idApp}", methods="delete", defaults={"_api_item_operation_name"="removeApprenantOfGroup"})
     */
    public function removeApprenantOfGroup(int $idGroup,int $idApp)
    {
            $groupe = $this->groupeRepository->findOneBy(["id" => $idGroup]);
            if(!$groupe){
                return new JsonResponse("Groupe inexistant");
            }
            $apprenant = $this->apprenantRepository->findOneBy(["id" => $idApp]);
            $groupe->removeApprenant($apprenant);
            $this->manager->flush();
            return new JsonResponse("Retiré");
        
    }
}
