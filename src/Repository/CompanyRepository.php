<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack, ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
        $this->requestStack = $requestStack;
    }

    public function findByFilterQuery()
    {
        $qb = $this->createQueryBuilder('company');
        $request = $this->requestStack->getCurrentRequest();

        $qb->andWhere('company.isVerified = 1');

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('company.name LIKE :filterKeyword OR company.description LIKE :filterKeyword OR company.email LIKE :filterKeyword')
                ->setParameter('filterKeyword', '%'.$request->query->get('keyword').'%');
        }

        return $qb->addOrderBy('company.created', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findByFilterQueryAdmin()
    {
        $qb = $this->createQueryBuilder('company');
        $request = $this->requestStack->getCurrentRequest();

        /* Check status */
        $status = $request->query->get('status');
        if (isset($status)) {
            $qb->andWhere('company.isVerified = :status')->setParameter('status', $status);
        }
        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('company.name LIKE :filterKeyword OR company.description LIKE :filterKeyword OR company.email LIKE :filterKeyword')
                ->setParameter('filterKeyword', '%'.$request->query->get('keyword').'%');
        }

        return $qb->addOrderBy('company.created', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findUserCompanies($user)
    {
        $qb = $this->createQueryBuilder('company');

        return $qb->innerJoin('company.users', 'user')
            ->andWhere('user.id = :user')
            ->setParameter('user', $user)
            ->orderBy('company.created', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findUserCompaniesQueryBuilder($user)
    {
        $qb = $this->createQueryBuilder('company');

        return $qb->innerJoin('company.users', 'user')
            ->andWhere('user.id = :user')
            ->setParameter('user', $user)
            ->orderBy('company.created', 'DESC');
    }

    public function hasUserCompany($user, $company)
    {
        $qb = $this->createQueryBuilder('company');

        $results = $qb->innerJoin('company.users', 'user')
            ->andWhere('user.id = :user')
            ->andWhere('company.id = :company')
            ->setParameters(
                [
                    'user' => $user,
                    'company' => $company,
                ]
            )
            ->getQuery()
            ->getResult();

        if (1 === count($results)) {
            return true;
        }

        return false;
    }
}
