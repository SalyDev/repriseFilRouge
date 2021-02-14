<?php
namespace App\DataPersister;

use App\Entity\Competence;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class CompetenceDataPersister implements ContextAwareDataPersisterInterface
{

  private $manager;
  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Competence;
    }

    public function persist($data, array $context = [])
    {
      // call your persistence layer to save $data
      $data->setArchive(false);
      if((($data->getNiveaux())->count()) < 3){
        $data->setEtat('incomplet');
      }
      else{
      $data->setEtat('complet');
      }
      $this->manager->persist($data);
      $this->manager->flush();
      return $data;
    }

    public function remove($data, array $context = [])
    {
        $data->setArchive(true);
        $grpsCompetences = $data->getGroupeCompetences();
        foreach ($grpsCompetences as $gc) {
          $data->removeGroupeCompetence($gc);
        }
        $this->manager->flush();
    }
}
