<?php

namespace App\DataProviders;

use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use App\Repository\ApprenantRepository;
use App\Repository\CompetenceRepository;
use App\Repository\GroupeCompetencesRepository;
use App\Repository\GroupeRepository;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Component\Validator\Constraints\Length;
use App\Repository\ProfilRepository;
use App\Repository\ProfilsortieRepository;
use App\Repository\PromoRepository;
use App\Repository\ReferentielRepository;
use App\Repository\UserRepository;

class AllEntitiesDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $profilRepository, $competenceRepository, $groupeCompetencesRepository, $groupeRepository, $profilsortieRepository, $referentielRepository, $promoRepository, $userRepository, $apprenantRepository;
    function __construct(ProfilRepository $profilRepository, CompetenceRepository $competenceRepository, GroupeCompetencesRepository $groupeCompetencesRepository, GroupeRepository $groupeRepository, ProfilsortieRepository $profilsortieRepository, ReferentielRepository $referentielRepository, PromoRepository $promoRepository, UserRepository $userRepository, ApprenantRepository $apprenantRepository)
    {
        $this->profilRepository = $profilRepository;
        $this->competenceRepository = $competenceRepository;
        $this->groupeCompetencesRepository = $groupeCompetencesRepository;
        $this->groupeRepository = $groupeRepository;
        $this->profilsortieRepository = $profilsortieRepository;
        $this->referentielRepository = $referentielRepository;
        $this->promoRepository = $promoRepository;
        $this->userRepository = $userRepository;
        $this->apprenantRepository = $apprenantRepository;
    }
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return true;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $entity="";
        for($i=11;$i<50;$i++){
            if(isset($resourceClass[$i])){
                $entity=$entity.$resourceClass[$i];
            }
        }
        $repositoryName = lcfirst($entity).'Repository';
        return $this->$repositoryName->findBy(["archive" => false]);     
    }
}