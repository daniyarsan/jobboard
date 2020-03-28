<?php

namespace App\Controller\Backend;

use App\Entity\Category;
use App\Form\AdminCategoryType;
use App\Form\AdminFilterType;
use App\Service\View\DataTransformer;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Admin Categories controller.
 *
 * @Route("/admin", name="admin_")
 */
class CategoriesController extends AbstractController
{
    /**
     * Lists all Category entities.
     *
     * @Route("/categories", name="category_index")
     * @Template("admin/categories/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $paginator)
    {
        $filterForm = $this->createForm(AdminFilterType::class);
        $filterForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Category')->createQueryBuilder('c');

        $itemsPerPage = $request->query->get('itemsPerPage', 20);
        $page = $request->query->get('page', 1);

        if ($session->get('pagesItemsPerPage') != $itemsPerPage) {
            $session->set('pagesItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('admin_category_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }
        $paginatorOptions = [
            'defaultSortFieldName' => 'c.name',
            'defaultSortDirection' => 'asc'
        ];

        $entities = $paginator->paginate($queryBuilder, $page, $itemsPerPage, $paginatorOptions);

        return [
            'entities' => $entities,
            'filter_form' => $filterForm->createView(),
            'bulk_action_form' => $this->createBulkActionForm()->createView()
        ];
    }

    /**
     * Create new Category entity.
     *
     * @Route("/category/create", name="category_create")
     * @Template("admin/categories/create.html.twig")
     */
    public function create(Request $request, DataTransformer $dataTransformer)
    {
        $entity = new Category();
        $form = $this->createForm(AdminCategoryType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setSlug($dataTransformer->slugify($entity->getName()));
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'The Category was successfully created.');

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirect($this->generateUrl('admin_category_index'));
            }
            return $this->redirect($this->generateUrl('admin_category_edit', ['id' => $entity->getId()]));
        }

        return [
            'form' => $form->createView(),
            'entity' => $entity
        ];
    }

    /**
     * Edit an existing Category entity.
     *
     * @Route("/category/{id}", name="category_edit", requirements={"id": "\d+"})
     * @ParamConverter("category", class="App\Entity\Category")
     *
     * @Template("admin/categories/edit.html.twig")
     */
    public function edit(Request $request, Category $category, TranslatorInterface $translator, DataTransformer $dataTransformer)
    {
        $form = $this->createForm(AdminCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $category->setSlug($dataTransformer->slugify($category->getName()));
                $em->persist($category);
                $em->flush();
                $this->addFlash('success', $translator->trans('Category has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_category_index');
            }
            return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/category/{action}/{id}", name="category_set", requirements={"id": "\d+", "action" : "remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:Category')->findBy(['id' => $id]);

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        foreach ($entities as $entity) {
            switch ($action) {
                case 'remove':
                    $em->remove($entity);
                    break;
            };
        }
        try {
            $em->flush();
        } catch (\Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_category_index')));
    }


    /**
     * Deletes, Enables and Disables selected Pages.
     *
     * @Route("/categories/bulk", name="category_bulk")
     */
    public function bulkAction(Request $request)
    {
        $form = $this->createBulkActionForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $id = array_keys($request->get('pages'));
            $action = $request->get('action');
            return $this->set($id, $action, $request);
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_category_index')));
    }

    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }
}
