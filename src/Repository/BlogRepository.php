<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    public function findByFilterQuery($request)
    {
        $qb = $this->createQueryBuilder('blog');
        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('blog.title LIKE :filterKeyword OR blog.slug LIKE :filterKeyword')
                ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        return $qb->addOrderBy('blog.created', 'DESC')
            ->getQuery()
            ->execute();
    }
}
