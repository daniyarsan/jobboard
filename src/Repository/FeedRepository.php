<?php

namespace App\Repository;

use App\Entity\Feed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Feed|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feed|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feed[]    findAll()
 * @method Feed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feed::class);
    }

    public function findByFilterQuery($request)
    {
        $qb = $this->createQueryBuilder('feed');

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('feed.name LIKE :filterKeyword OR feed.description LIKE :filterKeyword')
                ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        return $qb
            ->addOrderBy('feed.created', 'DESC')
            ->getQuery()
            ->execute();
    }


    public function findDefaultMappingFields()
    {
        $result = $this->createQueryBuilder('feed')
            ->select('feed.mapper_default')
            ->getQuery()
            ->getScalarResult();

        return array_map('current', $result);

    }
}
