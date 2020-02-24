<?php

namespace App\Repository;

use App\Entity\Field;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Field|null find($id, $lockMode = null, $lockVersion = null)
 * @method Field|null findOneBy(array $criteria, array $orderBy = null)
 * @method Field[]    findAll()
 * @method Field[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Field::class);
    }

    public function findByFilterQuery($request)
    {
        $qb = $this->createQueryBuilder('field');

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('field.name LIKE :filterKeyword')
                ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function findAllFieldIds()
    {
        $result = $this->createQueryBuilder('f')
            ->select('f.field_id')
            ->getQuery()
            ->getScalarResult();

        return array_map('current', $result);
    }
    public function findAllFieldNames()
    {
        $result = $this->createQueryBuilder('f')
            ->select('f.name')
            ->getQuery()
            ->getScalarResult();

        return array_map('current', $result);
    }

}
