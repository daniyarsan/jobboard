<?php

namespace App\Controller\Backend;

use App\Entity\Blog;
use App\Form\AdminBlogType;
use App\Form\AdminJobFilterType;
use App\Service\View\DataTransformer;
use App\Service\FileManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Blog controller.
 *
 * @Route("/admin", name="admin_")
 */
class BlogController extends AbstractController
{
    /**
     * Lists all blogs items.
     *
     * @Route("/blog", name="blog_index")
     * @Template("admin/blog/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $pagination)
    {
        $filterForm = $this->createForm(AdminJobFilterType::class);
        $filterForm->handleRequest($request);

        $itemsPerPage = $request->query->get('itemsPerPage', 20);
        $page = $request->query->get('page', 1);

        if ($session->get('pagesItemsPerPage') != $itemsPerPage) {
            $session->set('pagesItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('admin_blog_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }
        $paginatorOptions = [
            'defaultSortFieldName' => 'title',
            'defaultSortDirection' => 'asc'
        ];
        $entities = $this->getDoctrine()->getRepository('App:Blog')->findByFilterQuery($request);
        $entities = $pagination->paginate($entities, $page, $itemsPerPage, $paginatorOptions);

        return [
            'entities' => $entities,
            'filter_form' => $filterForm->createView(),
            'bulk_action_form' => $this->createBulkActionForm()->createView()
        ];
    }

    /**
     * Create a new Blog entity.
     *
     * @Route("/blog/create", name="blog_create")
     * @Template("admin/blog/create.html.twig")
     */
    public function create(Request $request, FileManager $fileManager, DataTransformer $dataTransformer)
    {
        $entity = new Blog();
        $form = $this->createForm(AdminBlogType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /* Logo Upload */
            if ($imageFile = $form['image']->getData()) {
                $entity->setImageName($fileManager->upload($imageFile, $this->getParameter('blog_images_dir')));
            }

            $em = $this->getDoctrine()->getManager();
            $entity->setSlug($dataTransformer->slugify($entity->getTitle()));
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'The blog was successfully saved.');
            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirect($this->generateUrl('admin_blog_index'));
            }
            return $this->redirect($this->generateUrl('admin_blog_edit', ['id' => $entity->getId()]));
        }

        return [
            'form' => $form->createView(),
            'entity' => $entity
        ];
    }

    /**
     * Edit an existing Blog entity.
     *
     * @Route("/blog/{id}", name="blog_edit", requirements={"id": "\d+"})
     * @ParamConverter("blog", class="App\Entity\Blog")
     *
     * @Template("admin/blog/edit.html.twig")
     */
    public function edit(Request $request, Blog $blog, TranslatorInterface $translator)
    {
        $form = $this->createForm(AdminBlogType::class, $blog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                $this->addFlash('success', $translator->trans('Blog has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_blog_index');
            }
            return $this->redirectToRoute('admin_blog_edit', ['id' => $blog->getId()]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/blog/{action}/{id}", name="blog_set", requirements={"id": "\d+", "action" : "disable|activate|remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:Blog')->findBy(['id' => $id]);

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Blog entity.');
        }

        foreach ($entities as $entity) {
            switch ($action) {
                case 'remove':
                    $em->remove($entity);
                    break;
                case 'disable':
                    $entity->setActive(false);
                    $em->persist($entity);
                    break;
                case 'activate':
                    $entity->setActive(true);
                    $em->persist($entity);
                    break;
            };
        }
        try {
            $em->flush();
        } catch (\Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_blog_index')));
    }


    /**
     * Deletes, Enables and Disables selected Blogs.
     *
     * @Route("/blog/bulk", name="blog_bulk")
     */
    public function bulkAction(Request $request)
    {
        $form = $this->createBulkActionForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $id = array_keys($request->get('blogs'));
            $action = $request->get('action');
            return $this->set($id, $action, $request);
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_blog_index')));
    }

    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }
}
