<?php
namespace App\DataPersister;

use App\Entity\Profilsortie;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class ProfilSortiePersister implements ContextAwareDataPersisterInterface
{
    private $manager;
  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Profilsortie;
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
      // on supprime ses relation avec les apprenants
      $apprenants = $data->getApprenants();
      foreach ($apprenants as $apprenant) {
        $data->removeApprenant($apprenant);
      }
      $this->manager->flush();
    }
}