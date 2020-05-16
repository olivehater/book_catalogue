<?php
/**
 * Author.
 */

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
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
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Repository\AuthorRepository $authorRepository Category repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="author_create",
     * )
     */
    public function create(Request $request, AuthorRepository $authorRepository): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author->setCreatedAt(new \DateTime());
            $author->setUpdatedAt(new \DateTime());
            $authorRepository->save($author);

            return $this->redirectToRoute('author_index');
        }

        return $this->render(
            'author/create.html.twig',
            ['form' => $form->createView()]
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
