<?php

namespace App\Repository;

use App\Entity\StaticPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StaticPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method StaticPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method StaticPage[]    findAll()
 * @method StaticPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaticPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaticPage::class);
    }

    public function findByFilterQuery($request)
    {
        $qb = $this->createQueryBuilder('static_page');

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('static_page.name LIKE :filterKeyword OR static_page.url LIKE :filterKeyword')
                ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        return $qb->addOrderBy('static_page.name', 'DESC')
            ->getQuery()
            ->execute();
    }
}
