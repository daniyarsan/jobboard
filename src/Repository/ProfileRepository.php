<?php

namespace App\Repository;

use App\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile[]    findAll()
 * @method Profile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    public function findByFilterQuery($request)
    {
        $qb = $this->createQueryBuilder('profile');

        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('profile.firstName LIKE :filterKeyword OR profile.lastName LIKE :filterKeyword OR profile.description LIKE :filterKeyword')
                ->setParameter('filterKeyword', '%'.$request->query->get('keyword').'%');
        }

        if (!empty($request->query->get('country'))) {
            $qb->andWhere('profile.country = :country')
                ->setParameter('country', $request->query->get('country'));
        }

        if (!empty($request->query->get('is_verified'))) {
            $qb->innerJoin('profile.user', 'user')
                ->andWhere('user.isVerified = 1');
        }

        return $qb->addOrderBy('profile.created', 'DESC')
            ->getQuery()
            ->execute();
    }
}
