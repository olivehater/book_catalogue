<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController.
 *
 * @Route("/category")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class CategoryController extends AbstractController
{
    /**
     * Category service.
     *
     * @var \App\Service\CategoryService
     */
    private $categoryService;

    /**
     * CategoryController constructor.
     *
     * @param \App\Service\CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Show all categories.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="category_index",
     *     methods={"GET"},
     * )
     */
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->categoryService->createPaginatedList($page);

        return $this->render(
            'category/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="category_create",
     * )
     */
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$category->setCreatedAt(new \DateTime());
            //$category->setUpdatedAt(new \DateTime());
            $this->categoryService->save($category);

            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request            HTTP request
     * @param \App\Entity\Category                      $category           Category entity
     * @param \App\Repository\CategoryRepository        $categoryRepository Category repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     name="category_edit",
     * )
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$category->setUpdatedAt(new \DateTime());
            $this->categoryService->save($category);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request  HTTP request
     * @param \App\Entity\Category                      $category Entity category
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     name="category_delete"
     * )
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($category->getBooks()->count()) {
            $this->addFlash('warning', 'message_category_contains_books');

            return $this->redirectToRoute('category_index');
        }

        $form = $this->createForm(FormType::class, $category, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);

            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }

    /**
     * Show selected category.
     *
     * @param \App\Entity\Category $category Category entity
     *
     * @return \Symfony\Component\HttpFoundation\Response Response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="category_show",
     * )
     */
    public function show(Category $category): Response
    {
        return $this->render(
            'category/show.html.twig',
            [
                'category' => $category,
            ]
        );
    }
}
