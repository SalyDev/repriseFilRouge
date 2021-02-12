<?php

namespace App\Controller;

use App\Entity\Promo;
use App\Entity\Groupe;
use App\Entity\Apprenant;
use App\Services\UserService;
use App\Repository\PromoRepository;
use App\Repository\GroupeRepository;
use App\Services\UploadAvatarService;
use App\Repository\ApprenantRepository;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReferentielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Security("is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM')")
 */
class PromoController extends AbstractController
{
    private $encoder, $manager, $referentielRepository, $uploadAvatarService, $validatorInterface, $promoRepository, $groupeRepository, $serializerInterface, $userService;
    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager, ReferentielRepository $referentielRepository, UploadAvatarService $uploadAvatarService, ValidatorInterface $validatorInterface, PromoRepository $promoRepository, GroupeRepository $groupeRepository, SerializerInterface $serializerInterface, UserService $userService)
    {
        $this->encoder = $encoder;
        $this->manager = $manager;
        $this->referentielRepository = $referentielRepository;
        $this->uploadAvatarService = $uploadAvatarService;
        $this->validatorInterface = $validatorInterface;
        $this->promoRepository = $promoRepository;
        $this->groupeRepository = $groupeRepository;
        $this->serializerInterface = $serializerInterface;
        $this->userService = $userService;
    }

    // fonction à utiliser pour l'ajout et la modification d'une promo
    public function editPromo($promo, $request)
    {
        $requete = $request->request;

        if ($request->files) {
            foreach ($request->files as $key => $value) {
                if ($key == "avatar") {
                    $fichier =  $this->uploadAvatarService->uploadAvatar($request, 'avatar');
                    $promo->setAvatar($fichier);
                }
            }
        }

        foreach ($requete as $key => $value) {
            if ($key != "referentiel" && $key != "groupes" && $key != "_method") {
                $setter = 'set' . ucfirst($key);
                $promo->$setter($value);
            }
            if ($key == "referentiel") {
                $referentiel = $this->referentielRepository->findOneBy(["libelle" => $value]);
                $promo->setReferentiel($referentiel);
            }
        }

        $this->validatorInterface->validate($promo);
        $this->manager->persist($promo);
        $this->manager->flush();
        return $this->json($promo, 200, []);
    }

    /**
     * @Route(path="api/admin/promos", name="addPromo", methods="POST", defaults={"_api_collection_operation_name"="addPromo"})
     */
    public function addPromo(Request $request)
    {
        $promo = new Promo;
        $groupe = new Groupe;
        $groupe->setType("principal");
        $promo->addGroupe($groupe);
        $groupe->setNom($promo->getTitre() . '-Groupe Principal');
        $this->manager->persist($groupe);
        return $this->editPromo($promo, $request);
    }

    // ajouter des apprenants à une promo de facon manuel ou par importation d'un fichier csv
    /**
     * @Route("api/admin/promos/{id}/apprenants", name="addApprenantsInPromo", methods={"POST"}, defaults={"_api_collection_operation_name"="addApprenantsInPromo"})
     */
    public function addApprenantsInPromo(int $id, Request $request, MailerInterface $mailer, ResetPasswordController $controller)
    {
        $requete = $request->request;
        $arrayOfApprenants = [];
        $promo = $this->promoRepository->findOneBy(['id' => $id]);
        // on trouve le groupe principal de la promo
        $groupePrincipal = $this->groupeRepository->findPrincipalGroup($promo);
     
        if ($requete->get('groupes')) {
            $groupes = $requete->get('groupes');
            $emails =  preg_split('/[, ]+/', $groupes);
            foreach ($emails as $value) {
                $pattern = '/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/';
                if (preg_match($pattern, $value)) 
                {
                    $apprenant = new Apprenant;
                    $apprenant->setEmail($value);
                    $apprenant->setNom("nom");
                    $apprenant->setPrenom("prenom");
                    $apprenant->setGenre("femme");
                    // on upload un avatar
                    $img = file_get_contents('https://source.unsplash.com/1080x720/?person');
                    $apprenant->setAvatar($img);

                    $apprenant->setHasJoinPromo(false);
                    $this->uploadAvatarService->giveRole("apprenant", $apprenant);
                    $apprenant->addGroupe($groupePrincipal);
                    $apprenant->setPassword($this->encoder->encodePassword($apprenant, "pass" . uniqid()));
                    $this->manager->persist($apprenant);
                    array_push($arrayOfApprenants, $apprenant);
                }
            }
        }

        //si on upload un fichier excel
        if (($request->files->get('file'))) {
            $arrayOfApprenants = $this->userService->uploadExcel($request, $groupePrincipal);
        }

        $this->manager->persist($promo);
        $this->manager->flush();

        foreach ($arrayOfApprenants as $student) {
            $controller->processSendingPasswordResetEmail($student->getEmail(), $mailer);
        }
        return $this->json($arrayOfApprenants, 200, []);
    }

    /**
     * @Route("api/admin/promos/{idPromo}/groupes/{idGrp}/apprenants", name="getAppOfGroupOfPromo", methods={"GET"}, defaults={"_api_item_operation_name"="getAppOfGroupOfPromo"})
     */
    public function getAppOfGroupOfPromo(int $idPromo, $idGrp)
    {
        $groupe = $this->groupeRepository->findOneBy(["id" => $idGrp]);
        $promo = $this->promoRepository->findByIdAndGroup($idPromo, $groupe);
        $promo = $this->serializerInterface->serialize($promo, 'json', ["groups" => ["promog3:read"]]);
        return new JsonResponse($promo, 200, [], true);
    }

    /**
     * @Route("api/admin/promos/{idPromo}/apprenants", name="updateStudentPromo", methods={"PUT"}, defaults={"_api_item_operation_name"="updateStudentPromo"})
     */
    public function removeOrAddStudentFromPromo(Request $request, $idPromo, ApprenantRepository $apprenantRepository)
    {
        $promo =  $this->userService->removeOrAddUserToPromo($request, $idPromo, $apprenantRepository, "apprenant", "Veuillez donner un mail d'etudiant");
        return $this->json($promo, 200, []);
    }

    /**
     * @Route("api/admin/promos/{idPromo}/formateurs", name="updateTeacherPromo", methods={"PUT"}, defaults={"_api_item_operation_name"="updateTeacherPromo"})
     */
    public function removeOrAddTeacherFromPromo(Request $request, $idPromo, FormateurRepository $formateurRepository)
    {
        $promo = $this->userService->removeOrAddUserToPromo($request, $idPromo, $formateurRepository, "formateur",  "Veuillez donner un mail de formateur");
        return $this->json($promo, 200, []);
    }

    /**
     * @Route("api/admin/promos/{idPromo}/groupes/{idGrp}", name="updateGroupStatus", methods={"PUT"}, defaults={"_api_item_operation_name"="updateGroupStatus"})
     */
    public function updateGroupStatus($idPromo, $idGrp, Request $request)
    {
        $request = $this->serializerInterface->decode($request->getContent(), "json");
        $groupe = $this->groupeRepository->findOneBy(["id" => $idGrp]);
        $promo = $this->promoRepository->findByIdAndGroup($idPromo, $groupe);
        if ($promo) {
            $groupe->setStatut($request["status"]);
            $this->manager->persist($groupe);
            $this->manager->flush();
            return $this->json($promo, 200, []);
        }
        return new JsonResponse("Opération impossible");
    }

    /**
     * @Route("api/admin/promos/{idPromo}/profilsorties/{idPs}", name="apprenant_promo_ps", methods={"GET"}, defaults={"_api_item_operation_name"="apprenant_promo_ps"})
     */
    public function apprenantsOfPsOfPromo(int $idPromo, int $idPs)
    {
        $apprenants = $this->promoRepository->showApprenantsOfPs($idPromo, $idPs);
        $apprenants = $this->serializerInterface->serialize($apprenants, "json", ["groups" => ["promo_ps:read"]]);
        return new JsonResponse($apprenants, 200, [], true);
    }

    // modification d'une promo

    /**
     * @Route("api/admin/promos/{id}", name="update_promo", methods={"PUT"}, defaults={"_api_item_operation_name"="update_promo"})
     */
    public function updatePromo(int $id, Request $request)
    {
        $promo = $this->promoRepository->findOneBy(["id" => $id]);
        // dd($promo);
        if ($promo) {
            return $this->editPromo($promo, $request);
        }
        else{
        return new JsonResponse("Promo inexistante");
        }
    }
}
