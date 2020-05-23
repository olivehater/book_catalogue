<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\Favourite;
use App\Repository\FavouriteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * Delete favourite action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request             HTTP request
     * @param \App\Entity\Favourite                     $favourite           Favourite entity
     * @param \App\Repository\FavouriteRepository       $favouriteRepository Favourite repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/favourite/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     name="favourite_delete"
     * )
     */
    public function deleteFavourite(Request $request, Favourite $favourite, FavouriteRepository $favouriteRepository): Response
    {
        $form = $this->createForm(FormType::class, $favourite, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $favouriteRepository->delete($favourite);

            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('user_favourite_index');
        }

        return $this->render(
            'user/delete_favourite.html.twig',
            [
                'form' => $form->createView(),
                'favourite' => $favourite,
            ]
        );
    }
}
