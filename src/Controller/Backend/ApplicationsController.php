<?php

namespace App\Controller\Backend;

use App\Entity\Application;
use App\Entity\Category;
use App\Form\AdminCategoryType;
use App\Form\ApplicationType;
use App\Service\Helper;
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
 * Admin Applications controller.
 *
 * @Route("/admin", name="admin_")
 */
class ApplicationsController extends AbstractController
{
    /**
     * Lists all Application entities.
     *
     * @Route("/applications", name="application_index")
     * @Method("GET")
     * @Template("admin/applications/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Application')->createQueryBuilder('a');

        $itemsPerPage = $request->query->get('itemsPerPage', 20);
        $page = $request->query->get('page', 1);

        if ($session->get('pagesItemsPerPage') != $itemsPerPage) {
            $session->set('pagesItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('admin_application_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }
        $paginatorOptions = [
            'defaultSortFieldName' => 'a.id',
            'defaultSortDirection' => 'asc'
        ];
        $applications = $paginator->paginate($queryBuilder, $page, $itemsPerPage, $paginatorOptions);

        return [
            'applications' => $applications,
            'bulk_action_form' => $this->createBulkActionForm()->createView()
        ];
    }

    /**
     * Edit an existing Application entity.
     *
     * @Route("/application/{id}", name="application_edit", requirements={"id": "\d+"})
     * @ParamConverter("application", class="App\Entity\Application")
     * @Template("admin/applications/edit.html.twig")
     */
    public function edit(Request $request, Application $application, TranslatorInterface $translator)
    {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $application = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($application);
                $em->flush();
                $this->addFlash('success', $translator->trans('Application has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_application_index');
            }
            return $this->redirectToRoute('admin_application_edit', ['id' => $application->getId()]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/application/{action}/{id}", name="application_set", requirements={"id": "\d+", "action" : "remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:Application')->findBy(['id' => $id]);

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Application entity.');
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
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_application_index')));
    }


    /**
     * Deletes, Enables and Disables selected Pages.
     *
     * @Route("/applications/bulk", name="application_bulk")
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
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_application_index')));
    }

    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }
}
