<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Promo;
use Doctrine\ORM\EntityManagerInterface;

final class PromoDataPersister implements ContextAwareDataPersisterInterface
{

  private $manager;
  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Promo;
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
      $groupes = $data->getGroupes();
      // on enleve les relations entre les groupes(apprenants et formateurs)
      // on archive les groupes
      foreach($groupes as $groupe){
        $groupe->setArchive(true);
        $data->removeGroupe($groupe);
      }
      $this->manager->flush();
    }
}
