<?php

namespace App\Controller;

use App\Repository\CompetenceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\GroupeCompetencesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class GcController extends AbstractController
{
    private $competenceRepository;
    function __construct(CompetenceRepository $competenceRepository)
    {
        $this->competenceRepository = $competenceRepository;
    }
   
    // fonction permettant de lister les competences completes
    
     /**
     * @Route("api/admin/competences/complet", name="getCompletCompetences", methods="GET", defaults={"_api_collection_operation_name"="getCompletCompetences"})
     */
    function getCompletCompetences(){
        $competencesComplet = $this->competenceRepository->findBy([
            'archive' => false,
            'etat' => 'complet'
        ]);
        return $this->json($competencesComplet, 200, []);
    }

    // fonction permettant de lister les competences incompletes
    /**
     * @Route("api/admin/competences/incomplet", name="getIncompletCompetences", methods="GET", defaults={"_api_collection_operation_name"="getIncompletCompetences"})
     */
    function getIncompletCompetences(){
        $competencesIncomplet = $this->competenceRepository->findBy([
            'archive' => false,
            "etat" => 'incomplet'
        ]);
        return $this->json($competencesIncomplet, 200, []);
    }
}
