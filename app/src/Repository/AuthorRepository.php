<?php
/**
 * Author repository.
 */
namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * @constant int
     */
    const PAGINATOR_ITEMS_PER_PAGE = 6;

    /**
     * AuthorRepository constructor.
     *
     * @param \Doctrine\Persistence\ManagerRegistry $registry Manager registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * Get or create new query.
     *
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder // tworzy lub pobiera query buildera
    {
        // jeśli nie ma query buildera to utworz nowy
        return $queryBuilder ?? $this->createQueryBuilder('author'); // task to alias
    }

    /**
     * Query all records.
     *
     * @return \Doctrine\ORM\QueryBuilder Query builer
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            //->select('category', 'task.id') // nie wiem czemu nie działa
            //->innerJoin('category.tasks', 'tasks')
            ->orderBy('author.title', 'ASC'); // domyśne sortowanie
    }
}