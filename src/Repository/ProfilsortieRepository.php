<?php

namespace App\Repository;

use App\Entity\Profilsortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Profilsortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profilsortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profilsortie[]    findAll()
 * @method Profilsortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilsortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profilsortie::class);
    }

    // /**
    //  * @return Profilsortie[] Returns an array of Profilsortie objects
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
    public function findOneBySomeField($value): ?Profilsortie
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
