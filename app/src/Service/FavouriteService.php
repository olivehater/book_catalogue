<?php
/**
 * Favourite service.
 */

namespace App\Service;

use App\Entity\Favourite;
use App\Repository\FavouriteRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class FavouriteService
 */
class FavouriteService
{
    /**
     * Favourite repository.
     *
     * @var \App\Repository\FavouriteRepository
     */
    private $favouriteRepository;

    /**
     * Paginator interface.
     *
     * @var \Knp\Component\Pager\PaginatorInterface
     */
    private $paginator;

    /**
     * FavouriteService constructor.
     *
     * @param \App\Repository\FavouriteRepository $favouriteRepository Favourite repository
     */
    public function __construct(FavouriteRepository $favouriteRepository, PaginatorInterface $paginator)
    {
        $this->favouriteRepository = $favouriteRepository;
        $this->paginator = $paginator;
    }

    /**
     * Create paginated list.
     *
     * @param int $page Page number
     * @param $user
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function createPaginatedList(int $page, $user): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->favouriteRepository->queryByUser($user),
            $page,
            FavouriteRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Already in user's favourites.
     *
     * @param $book
     * @param $user
     * @return \App\Entity\Favourite|null
     */
    public function alreadyInUsersFavourites($book, $user)
    {
        return $this->favouriteRepository->findOneBy([
            'book' => $book,
            'user' => $user,
        ]);
    }

    /**
     * Save favourite.
     *
     * @param \App\Entity\Favourite $favourite Favourite entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Favourite $favourite): void
    {
        $this->favouriteRepository->save($favourite);
    }

    /**
     * Delete favourite.
     *
     * @param \App\Entity\Favourite $favourite Favourite entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Favourite $favourite): void
    {
        $this->favouriteRepository->delete($favourite);
    }
}
