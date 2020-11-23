<?php
namespace App\DataPersister;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{

  private $manager;
  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function persist($data, array $context = [])
    {
      // call your persistence layer to save $data
      return $data;
    }

    public function remove($data, array $context = [])
    {
      $data->setArchive(true);
      $this->manager->flush();
    }
}
