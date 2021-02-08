<?php

namespace App\Repository;

use App\Entity\Promo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Promo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promo[]    findAll()
 * @method Promo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promo::class);
    }

    // /**
    //  * @return Promo[] Returns an array of Promo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Promo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

     //fonction permettant de trouver une promo suivant son id et son groupe
    public function findByIdAndGroup($idPromo, $groupe): ?Promo
    {
        return $this->createQueryBuilder('p')
            ->join('p.groupes', "groupes")
            ->where('p.id = :val1')
            ->andWhere('groupes = :val2')
            ->setParameter('val1', $idPromo)
            ->setParameter('val2', $groupe)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //les apprenants non archivés d'un type de groupe
    public function findApprenantsByGroupType($type, $id=null){
        $result= $this->createQueryBuilder('p')
            ->select('p,g,a')
            ->leftJoin('p.groupes', 'g')
            ->andWhere('g.type =:type')
            ->setParameter('type', $type)
            ->leftJoin('g.apprenants', 'a')
            ->andWhere('a.archive =:etat')
            ->setParameter('etat', false);
           
            if($id){
                $result->andWhere('p.id = :id')
                ->setParameter('id', $id)
                ;
            }
          return  $result->getQuery()
                         ->getResult();
        ;
    }


    // afficher les apprenants d'une promo par profil de sortie
    public function showApprenantsByPs($id){
        return $this->createQueryBuilder('p')
                    ->leftJoin('p.groupes', 'g')
                    ->andWhere('p.id = :val')
                    ->setParameter('val', $id)
                    ->leftJoin('g.apprenants','a')
                    ->leftJoin('a.profilsortie','ps')
                    ->groupBy('ps')
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
    }

    //afficher les apprenants d'un profil de sortie d'une promo
    public function showApprenantsOfPs($idPromo, $idPs){
        return $this->createQueryBuilder('p')
                    ->select('p,g,a,ps')
                    ->leftJoin('p.groupes', 'g')
                    ->andWhere('p.id = :idPromo')
                    ->setParameter('idPromo', $idPromo)
                    ->leftJoin('g.apprenants','a')
                    ->leftJoin('a.profilsortie','ps')
                    ->andWhere('ps.id = :idPs')
                    ->setParameter('idPs', $idPs)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
    }




    

}
