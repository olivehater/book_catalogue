<?php
/**
 * Book controller.
 */

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            ['pagination' => $pagination]
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
            ['book' => $book]
        );
    }
}
