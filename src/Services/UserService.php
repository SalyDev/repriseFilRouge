<?php
namespace App\Services;

use ApiPlatform\Core\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService{

    private $manager, $encoder, $serializer, $uploadAvatarService, $validator;

    function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer, UploadAvatarService $uploadAvatarService, ValidatorInterface $validator)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->uploadAvatarService = $uploadAvatarService;
        $this->validator = $validator;
    }

    public function addUser($request, $object, $message)
    {
        
        $object->setPrenom($request->get('prenom'));
        $object->setNom($request->get("nom"));
        $object->setPassword($this->encoder->encodePassword($object, $request->get('password')));
        $avatar = $this->uploadAvatarService->uploadAvatar($request);
        $object->setAvatar($avatar);
        $object->setEmail($request->get('email'));
        if(gettype($object) == "Apprenant"){
            $object->setAdresse($request->get("adresse"));
            $object->setTelephone($request->get("telephone"));
        }
        $erreurs[] = $this->validator->validate($object);
        // if(count($erreurs) > 0){
        //     dd("ererurs");
        // }
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
        foreach($tab as $user){
            $avatar = $user->getAvatar();
            if($avatar){
                $user->setAvatar(base64_encode(stream_get_contents($avatar)));
            }
        }
        $users = $this->serializer->serialize($users, "json");
        return new JsonResponse($users, 200, [], true);
    }
    public function showUsers($repository)
    {
        $users = $repository->findAll();  
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
        if($request->get("prenom")){
            $object->setPrenom($request->get("prenom"));
        }
        if($request->get("nom")){
            $object->setNom($request->get("nom"));
        }
        if($request->get("email")){
            $object->setEmail($request->get("email"));
        }
        if($request->get("avatar")){
            $avatar = $this->uploadAvatarService->uploadAvatar($request);
            $object->setAvatar($avatar);
        }
        if($request->get("password")){
            $object->setPassword($this->encoder->encodePassword($object, $request->get('password')));
        }
        if(gettype($object) == "Apprenant"){
            $object->setAdresse($request->get("adresse"));
            $object->setTelephone($request->get("telephone"));
        }
        $erreurs[] = $this->validator->validate($object);
        // if (count($erreurs) > 0){
        //     dd($erreurs);
        //     // return $this->json($erreurs,Response::HTTP_BAD_REQUEST);
        // }
        $this->manager->persist($object);
        $this->manager->flush();
        return new JsonResponse($succesMsg);
    }
}