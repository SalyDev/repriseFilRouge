<?php
namespace App\DataProviders;

use App\Entity\Promo;
use App\Repository\PromoRepository;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;

final class PromoItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $promoRepository;
    function __construct(PromoRepository $promoRepository){
        $this->promoRepository = $promoRepository;
    }
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Promo::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Promo
    {
        if($operationName == "item_apprenant_group_principal"){
            return $this->promoRepository->findApprenantsByGroupType("principal", $id)[0];
        }

        if($operationName == "apprenant_promo_profilsortie"){
            return $this->promoRepository->showApprenantsByPs($id);
        }
        return $this->promoRepository->findOneBy(["id" => $id]);
    }
}