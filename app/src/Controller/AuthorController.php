<?php
/**
 * Author.
 */

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController.
 *
 * @Route("/author")
 */
class AuthorController extends AbstractController
{
    /**
     * Show all authors.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param AuthorRepository $authorRepository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator Paginator interface
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="author_index",
     *     methods={"GET"},
     * )
     */
    public function index(Request $request, AuthorRepository $authorRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $authorRepository->queryAll(),
            $request->query->getInt('page', 1),
            AuthorRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'author/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show selected category.
     *
     * @param Author $author
     * @return \Symfony\Component\HttpFoundation\Response Response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="author_show",
     * )
     */
    public function show(Author $author): Response
    {
        return $this->render(
            'author/show.html.twig',
            ['author' => $author]
        );
    }
}
