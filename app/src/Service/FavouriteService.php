<?php
/**
 * Favourite service.
 */

namespace App\Service;

use App\Entity\Favourite;
use App\Repository\FavouriteRepository;

/**
 * Class FavouriteService
 */
class FavouriteService
{
    private $favouriteRepository;

    /**
     * FavouriteService constructor.
     *
     * @param \App\Repository\FavouriteRepository $favouriteRepository Favourite repository
     */
    public function __construct(FavouriteRepository $favouriteRepository)
    {
        $this->favouriteRepository = $favouriteRepository;
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
     * Save record.
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
}
