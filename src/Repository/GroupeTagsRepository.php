<?php

namespace App\Repository;

use App\Entity\GroupeTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupeTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupeTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupeTags[]    findAll()
 * @method GroupeTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupeTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupeTags::class);
    }

    // /**
    //  * @return GroupeTags[] Returns an array of GroupeTags objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupeTags
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
