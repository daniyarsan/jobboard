<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllOrderedByName()
    {
        $qb = $this->createQueryBuilder('category');

        return $qb->orderBy('category.name', 'ASC');
    }

    public function findAllFieldNames()
    {
        $result = $this->createQueryBuilder('c')
            ->select('c.name')
            ->getQuery()
            ->getScalarResult();

        return array_map('current', $result);
    }

    public function findAllFieldSlug()
    {
        $result = $this->createQueryBuilder('c')
            ->select('c.slug')
            ->getQuery()
            ->getScalarResult();

        return array_map('current', $result);
    }
}
