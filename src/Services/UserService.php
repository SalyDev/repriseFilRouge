<?php
namespace App\Services;

use App\Entity\CM;
use App\Entity\Admin;
use App\Entity\Apprenant;
use App\Entity\Formateur;
use App\Repository\GroupeRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Repository\ProfilsortieRepository;
use App\Repository\PromoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\Request;

class UserService{

    private $manager, $encoder, $serializer, $uploadAvatarService, $validator, $groupeRepository, $profilRepository, $promoRepository, $serializerInterface, $profilsortieRepository;

    function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer, UploadAvatarService $uploadAvatarService, ValidatorInterface $validator, ProfilRepository $profilRepository, GroupeRepository $groupeRepository, PromoRepository $promoRepository, SerializerInterface $serializerInterface, ProfilsortieRepository $profilsortieRepository)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->uploadAvatarService = $uploadAvatarService;
        $this->validator = $validator;
        $this->profilRepository = $profilRepository;
        $this->groupeRepository = $groupeRepository;
        $this->promoRepository = $promoRepository;
        $this->serializerInterface = $serializerInterface;
        $this->profilsortieRepository = $profilsortieRepository;
    }

    public function addUser($request, $object, $message)
    {
        $object->setPrenom($request->get('prenom'));
        $object->setNom($request->get("nom"));
        $object->setGenre($request->get("genre"));
        $object->setPassword($this->encoder->encodePassword($object, $request->get('password')));
        // if($request->get)
        if($request->files->get("avatar")){
        $avatar = $this->uploadAvatarService->uploadAvatar($request, "avatar");
        $object->setAvatar($avatar);
        }
        $object->setEmail($request->get('email'));
        if(gettype($object) == "Apprenant"){
            $object->setAdresse($request->get("adresse"));
            $object->setTelephone($request->get("telephone"));
            if($request->get("profilsortie")){
                $profilsortie = $this->profilsortieRepository->findOneBy([
                    "libelle" => $request->get("profilsortie")
                ]);
                $object->setProfilsortie($profilsortie);
            }
        }
        if($request->get('groupes')){
            $object->addGroupes($request->get('groupes'));
        }
        $this->validator->validate($object);
        $this->manager->persist($object);
        $this->manager->flush();
        return new JsonResponse($message);
    }

    public function show($users){
        if(gettype($users)=="array"){
            $tab=$users;
        }
        else{
            $tab[]=$users;
        }
        // foreach($tab as $user){
        //     $avatar = $user->getAvatar();
        //     if($avatar){
        //         $user->setAvatar(base64_encode(stream_get_contents($avatar)));
        //     }
        // }
        $users = $this->serializer->serialize($users, "json");
        return new JsonResponse($users, 200, [], true);
    }

    
    public function showUsers($users)
    {
        return $this->show($users);
    }

    public function showOneUser($id, $repository){
        $user = $repository->findOneBy(["id" => $id]);
        return $this->show($user);
    }

    public function updateUser($object, $request, $errorMsg, $succesMsg){
        if(!$object){
            return new JsonResponse($errorMsg);
        }
        $req = $request->request->all();
        if($request->get('groupes')){
            $groupe = $this->groupeRepository->findOneBy(["id" => $request->get('groupes')]);
            $object->addGroupe($groupe);
        }
        if($request->get("profilsortie")){
            $profilsortie = $this->profilsortieRepository->findOneBy([
                "libelle" => $request->get("profilsortie")
            ]);
            $object->setProfilsortie($profilsortie);
        }
        foreach ($req as $key => $value) {
            # code...
            if($key != "_method" && $key!="groupes" && $key!="profilsortie"){
                // if($key == "profil"){
                //    if($value == "admin"){
                //        $newObject = new Admin;
                //    }
                //    if($value == "cm"){
                //        $newObject = new CM;
                //    }
                //    if($value == "formateur"){
                //        $newObject = new Formateur();
                //    }
                //    dd($object["adresse"]);
                // }
                if($key == "password"){
                    $object->setPassword($this->encoder->encodePassword($object, $value));
                }
                else{
                    $setter = 'set'.ucfirst($key);
                    $object->$setter($value);
                }
               
            }
        }
        if($request->files->get("avatar")){
            $avatar = $this->uploadAvatarService->uploadAvatar($request, "avatar");
            $object->setAvatar($avatar);
        }
        
        $this->validator->validate($object);
        $this->manager->persist($object);
        $this->manager->flush();
        return new JsonResponse($succesMsg);
    }

    public function uploadExcel($request, $groupe){
        // dd($request);
         //on recupere le fichier de la requete
       $file = $request->files->get('file'); 
       //le dossier dans lequel on stocke les fichiers
       $fileFolder = __DIR__ . '/../../public/uploads/';
       //on genere un nom de path unique pour le fichier
       $filePathName = md5(uniqid()) . $file->getClientOriginalName();
                try {
                    $file->move($fileFolder, $filePathName);
                } catch (FileException $e) {
                    dd($e);
                }
        //on lit le fichier excel
        $spreadsheet = IOFactory::load($fileFolder . $filePathName);
        // on traduit le fichier en tableau
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $attributs = [
            "A" => "email",
            "B" => "prenom",
            "C" => "nom",
            "D" => "adresse",
            "E" => "telephone",
            "F" => "genre"
        ];
        foreach ($sheetData as $line) {
            $apprenant = new Apprenant();
            foreach ($line as $cle => $value) {
                if($cle=="A"){
                    $emails[]=$value;
                }
                $setter = 'set'.ucfirst($attributs[$cle]);
                $apprenant->setPassword($this->encoder->encodePassword($apprenant, "pass".uniqid()));
                $apprenant->$setter($value);
                $this->uploadAvatarService->giveRole("apprenant", $apprenant);
            }

            $request2 = new Request;
            $request2->files->set('avatar', ['https://source.unsplash.com/1080x720/?home']);
            $apprenant->setAvatar($request2, 'avatar');
            // dd($apprenant);
            $this->validator->validate($apprenant);
            $this->manager->persist($apprenant);
            $groupe->addApprenant($apprenant);  
        }
        return $emails;
     }

     //fonction permettant d'ajouter ou de supprimer un utilisateur d'un promo
    public function removeOrAddUserToPromo($request, $idPromo, $repository, $key, $message){
        $request = $this->serializerInterface->decode($request->getContent(), "json");
        $user = $repository->findOneBy(["email" => $request]);
        // if(gettype($user) != ucfirst($key)){
        //     return new JsonResponse($message, 400, [], true);
        // }
        $exist = true;
        $promo = $this->promoRepository->findOneBy(["id" => $idPromo]);
        $groupes = $promo->getGroupes();
        foreach ($groupes as $groupe) {
            $containSetter = 'get'.ucfirst($key).'s';
            if($groupe->$containSetter()->contains($user)){
                $setter = 'remove'.ucfirst($key);
                $groupe->$setter($user);
            }
            else{
                $exist = false;
            }
           
        }
        if($exist == false){
           $setter = 'add'.ucfirst($key);
           $groupePrincipal = $this->groupeRepository->findPrincipalGroup($promo);
           $groupePrincipal->$setter($user);
        }
        $this->manager->persist($groupe);
        $this->manager->persist($promo);
        $this->manager->flush();
        return $promo;
    }
}