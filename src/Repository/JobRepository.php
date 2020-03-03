<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function unfeature()
    {
        $qb = $this->createQueryBuilder('job');

        return $qb->update()
            ->set('job.isFeatured', '0')
            ->andWhere('job.featuredUntil > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->execute();
    }

    public function unpublish()
    {
        $qb = $this->createQueryBuilder('job');

        return $qb->update()
            ->set('job.isPublished', '0')
            ->andWhere('job.publishedUntil > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->execute();
    }

    public function activate()
    {
        $qb = $this->createQueryBuilder('job');

        return $qb->update()
            ->set('job.isPublished', '1')
            ->andWhere('job.publishedUntil > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->execute();
    }

    public function getCompanyCount($company)
    {
        $qb = $this->createQueryBuilder('job');
        $results = $qb->andWhere('job.company = :company')
            ->andWhere('job.isPublished = 1')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        return count($results);
    }

    /**
     * @param User $user
     * @param Job $job
     * @return bool
     */
    public function hasUserJob($user, $job)
    {
        if (!$user) {
            return false;
        }
        $qb = $this->createQueryBuilder('job');

        return $qb->andWhere('job.company = :company')
            ->andWhere('job.id = :job')
            ->setParameters(
                [
                    'company' => $user->getCompany(),
                    'job' => $job,
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findRecent($count = 8)
    {
        $qb = $this->createQueryBuilder('job');

        return $qb->andWhere('job.isPublished = 1')
            ->orderBy('job.created', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->execute();
    }

    public function findByCategorySlug($slug)
    {
        $qb = $this->createQueryBuilder('job');
        return $qb->innerJoin('job.categories', 'category')
            ->andWhere('category.slug = :slug')
            ->setParameter('slug', $slug)
            ->andWhere('job.isPublished = 1')
            ->addOrderBy('job.isFeatured', 'DESC')
            ->addOrderBy('job.created', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findUserJobs(User $user)
    {
        if (!$user) {
            return false;
        }
        $qb = $this->createQueryBuilder('job');

        return $qb->andWhere('job.company = :company')
            ->setParameter('company', $user->getCompany())
            ->orderBy('job.created', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findByFilterQuery($request)
    {
        $qb = $this->createQueryBuilder('job');
        if (!strstr($request->getPathInfo(), 'admin')) {
            $qb->andWhere('job.isPublished = 1');
        }
        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('job.title LIKE :filterKeyword OR job.description LIKE :filterKeyword')
                ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        // Categories
        if (!empty($request->query->get('categories'))) {
            $qb->leftJoin('job.categories', 'categories')
                ->andWhere('categories.id IN(:categories)')
                ->setParameter('categories', $request->query->get('categories'));
        }

        // Country
        if (!empty($request->query->get('country'))) {
            $qb->andWhere('job.country = :country')
                ->setParameter('country', $request->query->get('country'));
        }

        // Contracts
        if (!empty($request->query->get('contracts'))) {
            $qb->leftJoin('job.contract', 'contract')
                ->andWhere('contract.id IN(:contracts)')
                ->setParameter('contracts', $request->query->get('contracts'));
        }

        return $qb->addOrderBy('job.isFeatured', 'DESC')
            ->addOrderBy('job.created', 'DESC')
            ->getQuery()
            ->execute();
    }
}
