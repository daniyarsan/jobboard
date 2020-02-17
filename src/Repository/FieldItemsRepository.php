<?php

namespace App\Repository;

use App\Entity\FieldItems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FieldItems|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldItems|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldItems[]    findAll()
 * @method FieldItems[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldItemsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FieldItems::class);
    }

    // /**
    //  * @return FieldItems[] Returns an array of FieldItems objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FieldItems
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
