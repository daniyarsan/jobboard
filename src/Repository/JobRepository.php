<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{

    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack, ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
        $this->requestStack = $requestStack;
    }


    public function findByFilterQuery()
    {
        $qb = $this->createQueryBuilder('job');

        $request = $this->requestStack->getCurrentRequest();
        $qb->andWhere('job.active = 1');

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('job.title LIKE :filterKeyword')/*OR job.description LIKE :filterKeyword*/
            ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        // By Company
        if (!empty($request->query->get('company'))) {
            $qb->andWhere('job.company = :companyId')/*OR job.description LIKE :filterKeyword*/
            ->setParameter('companyId',   $request->query->get('company'));
        }

        // Categories
        if (!empty($request->query->get('categories'))) {
            $qb->leftJoin('job.categories', 'categories');
            $qb->andWhere('categories.name IN (:categories)')
                ->setParameter('categories', $request->query->get('categories'));
        }

        // Categories
        if (!empty($request->query->get('discipline'))) {
            $qb->leftJoin('job.discipline', 'discipline');
            $qb->andWhere('discipline.name IN (:discipline)')
                ->setParameter('discipline', $request->query->get('discipline'));
        }

        // Country
        if (!empty($request->query->get('state'))) {
            $qb->andWhere('job.state = :state')
                ->setParameter('state', $request->query->get('state'));
        }

        return $qb->addOrderBy('job.featured', 'DESC')
            ->addOrderBy('job.created', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findByFilterQueryAdmin()
    {
        $qb = $this->createQueryBuilder('job');

        $request = $this->requestStack->getCurrentRequest();

        /* Check status */
        $status = $request->query->get('status');
        if (isset($status)) {
            $qb->andWhere('job.active = :status')->setParameter('status', $status);
        }

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('job.title LIKE :filterKeyword')/*OR job.description LIKE :filterKeyword*/
            ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        // By Company
        if (!empty($request->query->get('company'))) {
            $qb->andWhere('job.company = :companyId')/*OR job.description LIKE :filterKeyword*/
            ->setParameter('companyId',   $request->query->get('company'));
        }

        // Categories
        if (!empty($request->query->get('categories'))) {
            $qb->leftJoin('job.categories', 'categories');
            $qb->andWhere('categories.name IN (:categories)')
                ->setParameter('categories', $request->query->get('categories'));
        }

        // Categories
        if (!empty($request->query->get('discipline'))) {
            $qb->leftJoin('job.discipline', 'discipline');
            $qb->andWhere('discipline.name IN (:discipline)')
                ->setParameter('discipline', $request->query->get('discipline'));
        }

        // Country
        if (!empty($request->query->get('state'))) {
            $qb->andWhere('job.state = :state')
                ->setParameter('state', $request->query->get('state'));
        }

        return $qb->addOrderBy('job.featured', 'DESC')
            ->addOrderBy('job.created', 'DESC')
            ->getQuery()
            ->execute();
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

        return $qb->andWhere('job.active = 1')
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
            ->andWhere('job.active = 1')
            ->addOrderBy('job.featured', 'DESC')
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

    public function deleteByFeedId($feedId)
    {
        $query = $this->createQueryBuilder('job')
            ->delete()
            ->where('job.feedId = :feedId')
            ->setParameter('feedId', $feedId)
            ->getQuery();

        return $query->execute();
    }

    public function unfeature()
    {
        $qb = $this->createQueryBuilder('job');

        return $qb->update()
            ->set('job.featured', '0')
            ->andWhere('job.featuredUntil > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->execute();
    }

    public function unpublish()
    {
        $qb = $this->createQueryBuilder('job');

        return $qb->update()
            ->set('job.active', '0')
            ->andWhere('job.publishedUntil > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->execute();
    }

    public function activate()
    {
        $qb = $this->createQueryBuilder('job');

        return $qb->update()
            ->set('job.active', '1')
            ->andWhere('job.publishedUntil > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->execute();
    }

    public function getFilterItemsCategories($request)
    {
        $qb = $this->createQueryBuilder('job');

        /* Job should be active */
        if (!strstr($request->getPathInfo(), 'admin')) {
            $qb->andWhere('job.active = 1');
        }

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('job.title LIKE :filterKeyword')/*OR job.description LIKE :filterKeyword*/
            ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        // Categories
        if (!empty($request->query->get('categories'))) {
            $qb->andWhere('categories.name IN (:categories)')
                ->setParameter('categories', $request->query->get('categories'));
        }

        // Country
        if (!empty($request->query->get('state'))) {
            $qb->andWhere('job.state = :state')
                ->setParameter('state', $request->query->get('state'));
        }

        return $qb
            ->select('COUNT(job.id) as count, categories.name as title')
            ->leftJoin('job.categories', 'categories')
            ->groupBy('categories')
            ->getQuery()
            ->getResult();
    }

    public function getFilterItemsState($request)
    {
        $qb = $this->createQueryBuilder('job');

        /* Job should be active */
        if (!strstr($request->getPathInfo(), 'admin')) {
            $qb->andWhere('job.active = 1');
        }

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('job.title LIKE :filterKeyword')/*OR job.description LIKE :filterKeyword*/
            ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        // Categories
        if (!empty($request->query->get('categories'))) {
            $qb->leftJoin('job.categories', 'categories');
            $qb->andWhere('categories.name IN (:categories)')
                ->setParameter('categories', $request->query->get('categories'));
        }

        // Country
        if (!empty($request->query->get('state'))) {
            $qb->andWhere('job.state = :state')
                ->setParameter('state', $request->query->get('state'));
        }

        return $qb
            ->select('COUNT(job.id) as count, job.state as title')
            ->groupBy('job.state')
            ->getQuery()
            ->getResult();
    }

    public function getStates($request)
    {
        $qb = $this->createQueryBuilder('job');
        /* Job should be active */
        if (!strstr($request->getPathInfo(), 'admin')) {
            $qb->andWhere('job.active = 1');
        }

        return $qb
            ->select('job.state as name')
            ->orderBy('job.state')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    public function getCategories($request)
    {
        $qb = $this->createQueryBuilder('job');
        /* Job should be active */
        if (!strstr($request->getPathInfo(), 'admin')) {
            $qb->andWhere('job.active = 1');
        }

        return $qb
            ->select('categories.name')
            ->leftJoin('job.categories', 'categories')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    public function getDisciplines($request)
    {
        $qb = $this->createQueryBuilder('job');
        /* Job should be active */
        if (!strstr($request->getPathInfo(), 'admin')) {
            $qb->andWhere('job.active = 1');
        }

        return $qb
            ->select('discipline.name')
            ->leftJoin('job.discipline', 'discipline')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    public function getFilterItemsDiscipline($request)
    {
        $qb = $this->createQueryBuilder('job');

        /* Job should be active */
        if (!strstr($request->getPathInfo(), 'admin')) {
            $qb->andWhere('job.active = 1');
        }

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('job.title LIKE :filterKeyword')/*OR job.description LIKE :filterKeyword*/
            ->setParameter('filterKeyword', '%' . $request->query->get('keyword') . '%');
        }

        // Categories
        if (!empty($request->query->get('discipline'))) {
            $qb->andWhere('discipline.name = :discipline')
                ->setParameter('discipline', $request->query->get('discipline'));
        }

        // Country
        if (!empty($request->query->get('state'))) {
            $qb->andWhere('job.state = :state')
                ->setParameter('state', $request->query->get('state'));
        }

        return $qb
            ->select('COUNT(job.id) as count, discipline.name as title')
            ->leftJoin('job.discipline', 'discipline')
            ->groupBy('discipline')
            ->getQuery()
            ->getResult();
    }

    public function getTotal()
    {
        return $this->createQueryBuilder('j')
            ->select('count(j)')
            ->getQuery()->getSingleScalarResult();
    }

    public function findForHomepage()
    {
        return $this->createQueryBuilder('job')
            ->select('COUNT(job.id) as count, discipline.name as name, discipline.slug as slug')
            ->leftJoin('job.discipline', 'discipline')
            ->where('discipline.name is not null')
            ->groupBy('discipline')
            ->getQuery()
            ->getResult();
    }

}
