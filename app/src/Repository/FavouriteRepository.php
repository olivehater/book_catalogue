<?php
/**
 * Favourite repository.
 */

namespace App\Repository;

use App\Entity\Favourite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Favourite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favourite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favourite[]    findAll()
 * @method Favourite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavouriteRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * @constant int
     */
    const PAGINATOR_ITEMS_PER_PAGE = 6;

    /**
     * FavouriteRepository constructor.
     *
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favourite::class);
    }

    /**
     * Save record.
     *
     * @param \App\Entity\Favourite $favourite Favourite entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Favourite $favourite): void
    {
        $this->_em->persist($favourite);
        $this->_em->flush($favourite);
    }

    /**
     * Delete record.
     *
     * @param \App\Entity\Favourite $favourite favourite entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Favourite $favourite): void
    {
        $this->_em->remove($favourite);
        $this->_em->flush($favourite);
    }

    /**
     * Query users favourites.
     *
     * @param \App\Entity\User|null $user User entity
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryByUser(User $user = null): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!is_null($user)) {
            $queryBuilder->andWhere('favourite.user = :user')
                ->setParameter('user', $user);
        }

        return $queryBuilder;
    }

    /**
     * Query all records.
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('favourite.createdAt', 'DESC');
    }

    /**
     * Get or create new query builder.
     *
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('favourite');
    }

    // /**
    //  * @return Favourite[] Returns an array of Favourite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Favourite
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
