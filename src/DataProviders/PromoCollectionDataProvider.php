<?php
namespace App\DataProviders;

use App\Entity\Promo;
use App\Repository\PromoRepository;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;

final class PromoCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $promoRepository;
    function __construct(PromoRepository $promoRepository){
        $this->promoRepository = $promoRepository;
    }
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Promo::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
       if($operationName == "apprenants_group_principal"){
            return $this->promoRepository->findApprenantsByGroupType("principal");
        }        
    }
}



