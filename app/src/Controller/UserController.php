<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\Favourite;
use App\Entity\User;
use App\Entity\UserData;
use App\Form\ChangePasswordType;
use App\Form\UserDataType;
use App\Repository\FavouriteRepository;
use App\Repository\UserDataRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP Request
     * @param \App\Repository\UserRepository            $userRepository User repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator      Pagination interface
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="user_index",
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $userRepository->queryAll(),
            $request->query->getInt('page', 1),
            UserRepository::PAGINATOR_ITEMS_FOR_PAGE
        );

        return $this->render(
            'user/index.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

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
            $repository->queryByUser($user), // to już blokuje wyświetlanie twojej listy przez innych użytkowników
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
     * @IsGranted("MANAGE", subject="favourite")
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

    /**
     * Change password action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\User                          $user       User entity
     * @param \App\Repository\UserRepository            $repository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @Route(
     *     "/{id}/changepassword",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_change_password",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="user"
     * )
     */
    public function changePassword(Request $request, User $user, UserRepository $repository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()
                )
            );
            $repository->save($user);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/change_password.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/changedata",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_data_change",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="userData"
     * )
     */
    public function changeData(Request $request, UserData $userData, UserDataRepository $repository): Response
    {
        $form = $this->createForm(UserDataType::class, $userData, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($userData);
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('user_show', ['id' => $userData->getUser()->getId()]);
        }

        return $this->render(
            'user/change_data.html.twig',
            [
                'form' => $form->createView(),
                'userData' => $userData,
            ]
        );
    }

    /**
     * Show user.
     *
     * @param \App\Entity\User $user User entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_show",
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="user"
     * )
     */
    public function show(User $user): Response
    {
        return $this->render(
            'user/show.html.twig',
            ['user' => $user]
        );
    }
}
