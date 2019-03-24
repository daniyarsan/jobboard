<?php

namespace App\Controller\Backend;

use App\Entity\StaticPage;
use App\Form\AdminStaticPageType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * StaticPage controller.
 *
 * @Route("/admin", name="admin_page")
 */
class PageController extends AbstractController
{
    /**
     * Lists all StaticPage entities.
     *
     * @Route("/pages", name="_index")
     * @Method("GET")
     * @Template("admin/pages/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:StaticPage')->createQueryBuilder('p');

        $itemsPerPage = $request->query->get('itemsPerPage', 20);
        $page = $request->query->get('page', 1);

        if ($session->get('pagesItemsPerPage') != $itemsPerPage) {
            $session->set('pagesItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('admin_page_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }
        $paginatorOptions = [
            'defaultSortFieldName' => 'p.name',
            'defaultSortDirection' => 'asc'
        ];
        $pages = $paginator->paginate($queryBuilder, $page, $itemsPerPage, $paginatorOptions);

        return [
            'pages' => $pages,
            'bulk_action_form' => $this->createBulkActionForm()->createView()
        ];
    }

    /**
     * Create a new StaticPage entity.
     *
     * @Route("/page/create", name="_create")
     * @Template("admin/pages/create.html.twig")
     */
    public function create(Request $request)
    {
        $entity = new StaticPage();
        $form = $this->createForm(AdminStaticPageType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'The page was successfully saved.');

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirect($this->generateUrl('admin_page_index'));
            }
            return $this->redirect($this->generateUrl('admin_page_edit', ['id' => $entity->getId()]));
        }

        return [
            'form' => $form->createView(),
            'entity' => $entity
        ];
    }

    /**
     * Edit an existing StaticPage entity.
     *
     * @Route("/page/{id}", name="_edit", requirements={"id": "\d+"})
     * @ParamConverter("staticPage", class="App\Entity\StaticPage")
     *
     * @Template("admin/pages/edit.html.twig")
     */
    public function edit(Request $request, StaticPage $staticPage, TranslatorInterface $translator)
    {
        $form = $this->createForm(AdminStaticPageType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                $this->addFlash('success', $translator->trans('Company has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_page_index');
            }
            return $this->redirectToRoute('admin_page_edit', ['id' => $staticPage->getId()]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/page/{action}/{id}", name="_set", requirements={"id": "\d+", "action" : "disable|activate|remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:StaticPage')->findBy(['id' => $id]);

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find StaticPage entity.');
        }

        foreach ($entities as $entity) {
            switch ($action) {
                case 'remove':
                    $em->remove($entity);
                    break;
                case 'disable':
                    $entity->setStatus(false);
                    $em->persist($entity);
                    break;
                case 'activate':
                    $entity->setStatus(true);
                    $em->persist($entity);
                    break;
            };
        }
        try {
            $em->flush();
        } catch (\Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_page_index')));
    }


    /**
     * Deletes, Enables and Disables selected Pages.
     *
     * @Route("/bulk", name="_bulk")
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
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_page_index')));
    }

    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }
}
