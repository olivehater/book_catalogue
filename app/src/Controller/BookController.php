<?php
/**
 * Book controller.
 */

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\Favourite;
use App\Entity\User;
use App\Form\BookType;
use App\Form\CommentType;
use App\Form\FavouriteType;
use App\Service\BookService;
use App\Service\CommentService;
use App\Service\FavouriteService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookController.
 *
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * Book service.
     *
     * @var \App\Service\BookService
     */
    private $bookService;

    /**
     * Favourite service.
     *
     * @var \App\Service\FavouriteService
     */
    private $favouriteService;

    private $commentService;

    /**
     * BookController constructor.
     */
    public function __construct(BookService $bookService, FavouriteService $favouriteService, CommentService $commentService)
    {
        $this->bookService = $bookService;
        $this->favouriteService = $favouriteService;
        $this->commentService = $commentService;
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
     *     name="book_index",
     * )
     */
    public function index(Request $request): Response
    {

        $page = $request->query->getInt('page', 1);
        $pagination = $this->bookService->createPaginatedList(
            $page,
            //$this->getUser(),
            $request->query->getAlnum('filters', [])
        );

        return $this->render(
            'book/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Repository\BookRepository            $bookRepository Book repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="book_create",
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->save($book);

            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Add favourite.
     *
     * @param \App\Entity\Book                          $book    Book entity
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/{id}/favourite",
     *     methods={"GET", "POST"},
     *     name="favourite_add"
     * )
     *
     * @IsGranted("ROLE_USER")
     */
    public function addFavourite(Book $book, Request $request): Response
    {
        $user = $this->getUser();
        /*
        $favourite = $favouriteRepository->findOneBy([
            'book' => $book,
            'user' => $this->getUser(),
        ]);
        */
        $favourite = $this->favouriteService->alreadyInUsersFavourites($book, $user);

        if (!$favourite) {
            $favourite = new Favourite();
            $form = $this->createForm(FavouriteType::class, $favourite);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $favourite->setUser($this->getUser());
                $favourite->setBook($book);
                //$favouriteRepository->save($favourite);
                $this->favouriteService->save($favourite);

                $this->addFlash('success', 'message_created_successfully');

                return $this->redirectToRoute('book_index');
            }
        } else {
            $this->addFlash('warning', 'message_already_in_favourites');

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'favourite/add.html.twig',
            [
                'form' => $form->createView(),
                'book' => $book,
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Book                          $book    Book entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     name="book_edit"
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->save($book);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/edit.html.twig',
            [
                'form' => $form->createView(),
                'book' => $book,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Book                          $book    Book entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     name="book_delete"
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($book->getFavourite()->count()) {
            $this->addFlash('warning', 'message_delete_favourites');

            return $this->redirectToRoute('book_index');
        }

        if ($book->getComment()->count()) {
            $this->addFlash('warning', 'message_delete_comments');

            return $this->redirectToRoute('book_index');
        }

        $form = $this->createForm(FormType::class, $book, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->delete($book);

            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/delete.html.twig',
            [
                'form' => $form->createView(),
                'book' => $book,
            ]
        );
    }

    /**
     * Delete comment.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Comment                       $comment Comment entity
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/{id}/deletecomment",
     *     methods={"GET", "DELETE"},
     *     name="comment_delete"
     * )
     * @IsGranted(
     *     "MANAGE",
     *     subject="comment"
     * )
     */
    public function deleteComment(Request $request, Comment $comment, $id): Response
    {
        $form = $this->createForm(FormType::class, $comment, ['method' => 'DELETE']);
        $form->handleRequest($request);

        //$book = $bookRepository->find($id); // szuka id książki
        $book = $this->bookService->findBookId($id);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            //$commentRepository->delete($comment);
            $this->commentService->delete($comment);

            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('book_show', ['id' => $comment->getBook()->getId()]); // dzięki temu wie gdzie wrócić
        }

        return $this->render(
            'book/delete_comment.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
                'book' => $book,
            ]
        );
    }

    /**
     * Add comment.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/{id}",
     *     methods={"GET", "POST"},
     *     name="add_comment",
     * )
     */
    public function addComment(Request $request, $id): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        //$book = $bookRepository->find($id);
        $book = $this->bookService->findBookId($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBook($book);
            $comment->setUser($this->getUser());
            //$commentRepository->save($comment);
            $this->commentService->save($comment);

            return $this->redirectToRoute('book_show', ['id' => $comment->getBook()->getId()]); //żeby wiedzieć pod jakie id wrócić
        }

        return $this->render(
            'book/show.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
                'book' => $book,
            ]
        );
    }

    /**
     * Show action.
     *
     * @param \App\Entity\Book $book Book entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="book_show",
     * )
     */
    public function show(Book $book): Response
    {
        return $this->render(
            'book/show.html.twig',
            [
                'book' => $book,
            ]
        );
    }
}
