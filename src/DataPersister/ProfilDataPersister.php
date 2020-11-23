<?php
namespace App\DataPersister;

use App\Entity\Profil;
use App\Repository\UserRepository;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;

final class ProfilDataPersister implements ContextAwareDataPersisterInterface
{

  private $userRepository, $manager;
  public function __construct(UserRepository $userRepository, EntityManagerInterface $manager)
  {
    $this->userRepository = $userRepository;
    $this->manager = $manager;
  }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Profil;
    }

    public function persist($data, array $context = [])
    {
      // call your persistence layer to save $data
      return $data;
    }

    public function remove($data, array $context = [])
    {
      $data->setArchive(true);
      $users = $data->getUsers();
      foreach($users as $user){
        $user->setArchive(true);
      }
      $this->manager->flush();
    }
}
