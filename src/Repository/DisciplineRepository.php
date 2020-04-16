<?php

namespace App\Repository;

use App\Entity\Discipline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Discipline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discipline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discipline[]    findAll()
 * @method Discipline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discipline::class);
    }

    public function findDisciplineByKeyword($keyword)
    {
        $result = $this->createQueryBuilder('d')
            ->where('d.name = :keyword')
            ->setParameter('keyword', $keyword)
            ->orWhere('d.synonyms LIKE :disciplineLike')
            ->setParameter('disciplineLike', '%' . $keyword . '%')
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }
}
