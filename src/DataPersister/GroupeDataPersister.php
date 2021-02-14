<?php

namespace App\DataPersister;

use App\Entity\Groupe;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;

class GroupeDataPersister implements ContextAwareDataPersisterInterface
{
    private $manager;
    function __construct(EntityManagerInterface $manager)
    {
      $this->manager = $manager;  
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Groupe;
    }

    public function persist($data, array $context = [])
    {
      // call your persistence layer to save $data
      $this->manager->persist($data);
      $this->manager->flush();
      return $data;
    }

    public function remove($data, array $context = [])
    {
      $data->setArchive(true);
      // on enleve les relations entre les promos, les apprenants et les formateurs
      $this->manager->flush();
    }
}