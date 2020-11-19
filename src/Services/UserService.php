<?php
namespace App\Services;

use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService{

    private $manager, $userRepository, $encoder, $serializer, $uploadAvatarService;

    function __construct(EntityManagerInterface $manager, UserRepository $userRepository, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer, UploadAvatarService $uploadAvatarService)
    {
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->uploadAvatarService = $uploadAvatarService;
    }

    public function addUser($request, $object, $message)
    {
        $emailExists = $this->userRepository->findOneBy(["email" => $request->get('email')]);
        if($emailExists){
            return new JsonResponse("Un utilisateur avec cet email existe deja");
        }
        $avatar = $this->uploadAvatarService->uploadAvatar($request);
        $object->setEmail($request->get('email'));
        $object->setPrenom($request->get('prenom'));
        $object->setNom($request->get("nom"));
        $object->setPassword($this->encoder->encodePassword($object, $request->get('password')));
        $object->setAvatar($avatar);
        $this->manager->persist($object);
        $this->manager->flush();
        return new JsonResponse($message);
    }

    public function showUsers($repository)
    {
        $users = $repository->findAll();  
        foreach($users as $user){
            $avatar = $user->getAvatar();
            if($avatar){
                $user->setAvatar(base64_encode(stream_get_contents($avatar)));
            }
        }
        $users = $this->serializer->serialize($users, "json");
        return new JsonResponse($users, 200, [], true);
    }

    public function updateUser($object,Request $request){
        // dd($request);
        if($request->get("prenom")){
            $object->setPrenom($request->get("prenom"));
        }
        $this->manager->persist($object);
        $this->manager->flush();
        return new JsonResponse('modifié');
    }
}