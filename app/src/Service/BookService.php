<?php
/**
 * Book service.
 */

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BookService.
 */
class BookService
{
    /**
     * Book repository.
     *
     * @var \App\Repository\BookRepository
     */
    private $bookRepository;

    /**
     * Paginator interface.
     *
     * @var \Knp\Component\Pager\PaginatorInterface
     */
    private $paginator;

    /**
     * Category service.
     *
     * @var \App\Service\CategoryService
     */
    private $categoryService;

    /**
     * Tag service.
     *
     * @var \App\Service\TagService
     */
    private $tagService;

    /**
     * BookService constructor.
     *
     * @param \App\Repository\BookRepository          $bookRepository  Book repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator       Paginator interface
     * @param \App\Service\CategoryService            $categoryService Category service
     */
    public function __construct(BookRepository $bookRepository, PaginatorInterface $paginator, CategoryService $categoryService, TagService $tagService)
    {
        $this->bookRepository = $bookRepository;
        $this->paginator = $paginator;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
    }

    /**
     * Create paginated list.
     *
     * @param int $page Page number
     * @param array $filters
     * @return \Knp\Component\Pager\Pagination\PaginationInterface Pagination interface
     */
    public function createPaginatedList(int $page, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
          $this->bookRepository->queryAll($filters),
          $page,
          BookRepository::PAGINATOR_ITEMS_FOR_PAGE
        );
    }

    /**
     * Save book.
     *
     * @param \App\Entity\Book $book Book entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Book $book): void
    {
        $this->bookRepository->save($book);
    }

    /**
     * Delete book.
     *
     * @param \App\Entity\Book $book Book entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Book $book): void
    {
        $this->bookRepository->delete($book);
    }

    /**
     * Find id for the book.
     *
     * @return \App\Entity\Book|null Book entity
     */
    public function findBookId(int $id)
    {
        return $this->bookRepository->find($id);
    }

    /**
     * Prepare filters for the tasks list.
     *
     * @param array $filters Raw filters from request
     *
     * @return array Result array of filters
     */
    private function prepareFilters(array $filters): array
    {
        $resultFilters = [];
        if (isset($filters['category']) && is_numeric($filters['category'])) {
            $category = $this->categoryService->findOneById(
                $filters['category']
            );
            if (null !== $category) {
                $resultFilters['category'] = $category;
            }
        }

        if (isset($filters['tag']) && is_numeric($filters['tag'])) {
            $tag = $this->tagService->findOneById($filters['tag']);
            if (null !== $tag) {
                $resultFilters['tag'] = $tag;
            }
        }

        return $resultFilters;
    }
}
