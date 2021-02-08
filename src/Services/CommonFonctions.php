<?php
namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CommonFonctions
{
    private $validatorInterface, $manager, $serializerInterface;
    function __construct(ValidatorInterface $validatorInterface, EntityManagerInterface $manager, SerializerInterface $serializerInterface)
    {
        $this->validatorInterface = $validatorInterface;
        $this->manager = $manager;
        $this->serializerInterface = $serializerInterface;
    }

    //fonction utilisé pour l'update de referentiels et de groupes de competences
    function updateObject($object, $theKey, $request, $repository, $tab){
        $request = $this->serializerInterface->decode($request->getContent(), "json");
        foreach ($request as $key => $value) {
            if($key != $theKey){
                $setter = 'set'.$key;
                $object->$setter($value);
            }
        }
        foreach ($request[$key] as $key => $valeur) {
            $objectModified = $repository->findOneBy(["libelle" => $valeur["libelle"]]);
            if($valeur["action"]=="retiré" && $tab->contains($objectModified)){
                $setter = 'remove'.ucfirst($theKey);
                $object->$setter($objectModified);
            }
            if($valeur["action"]=="ajouté" && !($tab->contains($objectModified))){
                $setter = 'add'.ucfirst($theKey);
                $object->$setter($objectModified);
            }
        }
        $this->validatorInterface->validate($object);
        $this->manager->persist($object);
        $this->manager->flush();
    }

    //fonction permettant d'ajouter un objet existant
    public function addExistingObject($cle, $object, $mainObject){
            $setter = 'add'.ucfirst($cle);
            $mainObject->$setter($object);
            $this->manager->persist($mainObject);
    }

    //fonction qui permet d'ajouter un objet inexistant
    public function addNotInexistantObject($value, $entity){
        $object = $this->serializerInterface->denormalize($value, $entity);
        $this->validatorInterface->validate($object);
        $this->manager->persist($object);

    }

}