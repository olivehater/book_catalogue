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
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use App\Repository\FavouriteRepository;
use Knp\Component\Pager\PaginatorInterface;
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
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP Request
     * @param \App\Repository\BookRepository            $bookRepository Book repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator      Pagination interface
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="book_index",
     * )
     */
    public function index(Request $request, BookRepository $bookRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $bookRepository->queryAll(),
            $request->query->getInt('page', 1),
            BookRepository::PAGINATOR_ITEMS_FOR_PAGE
        );

        return $this->render(
        'book/index.html.twig',
        [
            'pagination' => $pagination,
        ]
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
    public function create(Request $request, BookRepository $bookRepository): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookRepository->save($book);
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
     * @param \App\Entity\Book                          $book                Book entity
     * @param \Symfony\Component\HttpFoundation\Request $request             HTTP request
     * @param \App\Repository\FavouriteRepository       $favouriteRepository Favourite repository
     * @param \App\Repository\BookRepository            $bookRepository      Book repository
     * @param User                                      $user
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/{id}/favourite",
     *     methods={"GET", "POST"},
     *     name="favourite_add"
     *     )
     */
    public function addFavourite(Book $book, Request $request, FavouriteRepository $favouriteRepository, BookRepository $bookRepository): Response
    {
        $favourite = new Favourite();
        $form = $this->createForm(FavouriteType::class, $favourite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $favourite->setUser($this->getUser());
            $favourite->setBook($book);
            $favouriteRepository->save($favourite);

            $this->addFlash('success', 'message_created_successfully');

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
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\Book                          $book           Book entity
     * @param \App\Repository\BookRepository            $bookRepository Book repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     name="book_edit"
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Book $book, BookRepository $bookRepository): Response
    {
        $form = $this->createForm(BookType::class, $book, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookRepository->save($book);
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
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\Book                          $book           Book entity
     * @param \App\Repository\BookRepository            $bookRepository Book repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     name="book_delete"
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Book $book, BookRepository $bookRepository): Response
    {
        $form = $this->createForm(FormType::class, $book, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $bookRepository->delete($book);
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
     * Add comment.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request           HTTP request
     * @param \App\Repository\CommentRepository         $commentRepository Comment repository
     * @param \App\Repository\BookRepository            $bookRepository    Book repository
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/{id}",
     *     methods={"GET", "POST"},
     *     name="new_comment",
     * )
     */
    public function addComment(Request $request, CommentRepository $commentRepository, BookRepository $bookRepository, $id): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $book = $bookRepository->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBook($book);
            $comment->setUser($this->getUser());
            $commentRepository->save($comment);

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
     * Delete comment.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request           HTTP request
     * @param \App\Entity\Comment                       $comment           Comment entity
     * @param \App\Repository\CommentRepository         $commentRepository Comment repository
     * @param \App\Repository\BookRepository            $bookRepository    Book repository
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
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
    public function deleteComment(Request $request, Comment $comment, CommentRepository $commentRepository, BookRepository $bookRepository, $id): Response
    {
        $form = $this->createForm(FormType::class, $comment, ['method' => 'DELETE']);
        $form->handleRequest($request);

        $book = $bookRepository->find($id); // szuka id książki

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->delete($comment);
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
     *     requirements={"id": "[1-9]\d*"},
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
