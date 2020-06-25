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
use App\Service\FavouriteService;
use App\Service\UserDataService;
use App\Service\UserService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @var UserService
     */
    private $userService;

    /**
     * @var FavouriteService
     */
    private $favouriteService;

    /**
     * @var UserDataService
     */
    private $userDataService;

    /**
     * UserController constructor.
     *
     * @param UserService      $userService
     * @param FavouriteService $favouriteService
     * @param UserDataService  $userDataService
     */
    public function __construct(UserService $userService, FavouriteService $favouriteService, UserDataService $userDataService)
    {
        $this->userService = $userService;
        $this->favouriteService = $favouriteService;
        $this->userDataService = $userDataService;
    }

    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
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
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->userService->createPaginatedList($page);

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
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/favourite",
     *     methods={"GET"},
     *     name="user_favourite_index",
     * )
     */
    public function showFavourites(Request $request)
    {
        $user = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $favourite = $this->favouriteService->createPaginatedList($page, $user);

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
     * @param \Symfony\Component\HttpFoundation\Request $request   HTTP request
     * @param \App\Entity\Favourite                     $favourite Favourite entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/favourite/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     name="favourite_delete"
     * )
     * @IsGranted("MANAGE", subject="favourite")
     */
    public function deleteFavourite(Request $request, Favourite $favourite): Response
    {
        $form = $this->createForm(FormType::class, $favourite, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            //$favouriteRepository->delete($favourite);
            $this->favouriteService->delete($favourite);

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
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\User                          $user    User entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
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
    public function changePassword(Request $request, User $user): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword( // zakodowane hasło
                $this->userService->encodingPassword($user)
            );
            //$repository->save($user);
            $this->userService->save($user);

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
     * Change user's data.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request  HTTP request
     * @param \App\Entity\UserData                      $userData User data entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/{id}/changedata",
     *     methods={"GET", "PUT"},
     *     name="user_data_change",
     * )
     *
     * @IsGranted("MANAGE", subject="userData")
     */
    public function changeData(Request $request, UserData $userData): Response
    {
        $form = $this->createForm(UserDataType::class, $userData, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userDataService->save($userData);
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
