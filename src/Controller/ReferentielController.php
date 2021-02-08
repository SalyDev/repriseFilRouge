<?php

namespace App\Controller;

// use App\Entity\Referentiel;
use App\Services\CommonFonctions;
use Doctrine\ORM\EntityManagerInterface;
// use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\GroupeCompetencesRepository;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Referentiel;
use App\Repository\ReferentielRepository;
use App\Services\UploadAvatarService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReferentielController extends AbstractController
{
    private $manager, $validatorInterface, $uploadAvatarService, $groupeCompetencesRepository, $referentielRepository;
    function __construct(EntityManagerInterface $manager, ValidatorInterface $validatorInterface, UploadAvatarService $uploadAvatarService, GroupeCompetencesRepository $groupeCompetencesRepository, ReferentielRepository $referentielRepository)
    {
        $this->manager = $manager;
        $this->validatorInterface = $validatorInterface;
        $this->uploadAvatarService = $uploadAvatarService;
        $this->groupeCompetencesRepository = $groupeCompetencesRepository;
        $this->referentielRepository = $referentielRepository;
    }
    /**
     * @Route("api/admin/referentiels/{idRef}/grpecompetences/{idGc}", name="compOfReferentief", methods={"GET"}, defaults={"_api_item_operation_name"="compOfReferentief"})
     */
    public function showCompetencesofAReferentiel(int $idRef, int $idGc, GroupeCompetencesRepository $gcRepository, SerializerInterface $serializer, ReferentielRepository $referentielRepository)
    {
        $referentiel = $referentielRepository->findOneBy(["id" => $idRef]);
        $groupeCompetences = $gcRepository->findOneBy(["id" => $idGc]);
        if (!$referentiel) {
            return new JsonResponse("Referentiel inexistant");
        }
        $competences = $groupeCompetences->getCompetences();
        $competences = $serializer->serialize($competences, "json");
        return new JsonResponse($competences, Response::HTTP_OK, [], true);
    }

    public function setReferentielValues(Request $request, $referentiel){
        $requete = $request->request;
        foreach ($requete as $key => $value) {
            if ($key != "programme" && $key != 'groupeCompetences' && $key != "_method") {
                $setter = 'set' . ucfirst($key);
                $referentiel->$setter($value);
            }

            if ($key == 'groupeCompetences') {
                $libelles =  preg_split('/[,]+/', $value);
                // on recupere les groupe de compétences du referentiel
                $groupeComps = $referentiel->getGroupeCompetences();
                // on reinitialise le tableau
                if($groupeComps->count() > 0){
                    foreach ($groupeComps as $groupeComp) {
                        $referentiel->removeGroupeCompetence($groupeComp);
                    }
                }
                foreach ($libelles as $libelle) {
                    $gc = $this->groupeCompetencesRepository->findOneBy([
                        'libelle' => $libelle
                    ]);
                    if($gc)
                    {
                        $referentiel->addGroupeCompetence($gc);
                    }
                }
            }
        }

        if ($request->files) {
            foreach ($request->files as $key => $value) {
                if($key=="programme"){
                    $programme = $this->uploadAvatarService->uploadAvatar($request, 'programme');
                    $referentiel->setProgramme($programme);
                }
            }
        };
        $this->validatorInterface->validate($referentiel);
        $this->manager->persist($referentiel);
        $this->manager->flush();
        return $this->json($referentiel, Response::HTTP_OK, []);

    }

    //    ajout de referentiel
    /**
     * @Route("api/admin/referentiels", name="addReferentiel", methods={"POST"}, defaults={"_api_collection_operation_name"="addReferentiel"})
     */

    public function addReferentiel(Request $request)
    {
        $referentiel = new Referentiel();
        return $this->setReferentielValues($request, $referentiel);
    }

    // fonction pour la modification d'un referentiel

       /**
     * @Route("api/admin/referentiels/{id}", name="updateReferentiel", methods={"PUT"}, defaults={"_api_item_operation_name"="updateReferentiel"})
     */

     public function updateReferentiel(Request $request, int $id){
        $referentiel = $this->referentielRepository->findOneBy(["id" => $id]);
        return $this->setReferentielValues($request, $referentiel);
     }

}
