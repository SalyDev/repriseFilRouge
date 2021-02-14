<?php
namespace App\DataPersister;

use App\Entity\GroupeCompetences;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class GroupeCompetencesPersister implements ContextAwareDataPersisterInterface
{
  private $manager;
  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof GroupeCompetences;
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
      // on enleve les relations entre les competences et les referentiels
      $competences = $data->getCompetences();
      $referentiels = $data->getReferentiel();
      foreach ($competences as $competence) {
        $data->removeCompetence($competence);
      }
      foreach ($referentiels as $referentiel) {
        $data->removeReferentiel($referentiel);
      }

      $this->manager->flush();
    }
}
