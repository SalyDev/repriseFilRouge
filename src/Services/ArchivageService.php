<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class ArchivageService{

    private $objectManager;
    private $serializer;

    function __construct(EntityManagerInterface $objectManager, SerializerInterface $serializer)
    {
        $this->objectManager = $objectManager;
        $this->serializer = $serializer;
    }
    
    // fonction pour l'archivage
    public function archiver($id, $objectRepository){
        $object = $objectRepository->findOneBy(["id" => $id]);
        $object->setEtat("archivé");
        $this->objectManager->flush();
        return new JsonResponse("Archivé");
    }

    // fonction pour afficher l'ensemble des elements non archivés
    public function getElementsNoArchives($objectRepository){
        $elementNoArchives = $objectRepository->findBy([
            "etat" => null
        ]);
        $toJson = $this->serializer->serialize($elementNoArchives,"json");
        return new JsonResponse($toJson, Response::HTTP_OK,[],true );
    }
}