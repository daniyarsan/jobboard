<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
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

    public function findByFilterQuery($request)
    {
        $qb = $this->createQueryBuilder('company');

        if (!strstr($request->getPathInfo(), 'admin')) {
            $qb->andWhere('company.isVerified = 1');
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
}
