<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Repository\FavouriteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route(
 *     "/user"
 * )
 */
class UserController extends AbstractController
{
    /**
     * User's favourites.
     *
     * @param \App\Repository\FavouriteRepository       $repository Favourite repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator interface
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/favourite",
     *     methods={"GET"},
     *     name="user_favourite_index",
     * )
     */
    public function showFavourites(FavouriteRepository $repository, PaginatorInterface $paginator, Request $request)
    {
        $user = $this->getUser();
        $favourite = $paginator->paginate(
            $repository->queryByUser($user),
            $request->query->getInt('page', 1),
            FavouriteRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'user/favourite.html.twig',
            [
                'favourite' => $favourite,
                'user' => $user,
            ]
        );
    }
}
