<?php
namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Referentiel;

class ReferentielDataPersister implements ContextAwareDataPersisterInterface
{
    private $manager;
  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Referentiel;
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
      // on supprime ses relations avec les promos et le groupes de compétences
      $promos = $data->getPromos();
      $grpsCompetences = $data->getGroupeCompetences();

      foreach ($promos as $promo) {
        $data->removePromo($promo);
      }
      foreach ($grpsCompetences as $gc) {
        $data->removeGroupeCompetence($gc);
      }
      $this->manager->flush();
    }
}