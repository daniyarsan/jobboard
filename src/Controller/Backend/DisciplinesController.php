<?php

namespace App\Controller\Backend;

use App\Entity\Discipline;
use App\Form\AdminDisciplineType;
use App\Form\AdminJobFilterType;
use App\Service\View\DataTransformer;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Admin Disciplines controller.
 *
 * @Route("/admin", name="admin_")
 */
class DisciplinesController extends AbstractController
{
    /**
     * Lists all Discipline entities.
     *
     * @Route("/disciplines", name="discipline_index")
     * @Template("admin/disciplines/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $paginator)
    {
        $filterForm = $this->createForm(AdminJobFilterType::class);
        $filterForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Discipline')->createQueryBuilder('d');

        $itemsPerPage = $request->query->get('itemsPerPage', 20);
        $page = $request->query->get('page', 1);

        if ($session->get('pagesItemsPerPage') != $itemsPerPage) {
            $session->set('pagesItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('admin_discipline_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }
        $paginatorOptions = [
            'defaultSortFieldName' => 'd.name',
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
     * Create new Discipline entity.
     *
     * @Route("/discipline/create", name="discipline_create")
     * @Template("admin/disciplines/create.html.twig")
     */
    public function create(Request $request, DataTransformer $dataTransformer)
    {
        $entity = new Discipline();
        $form = $this->createForm(AdminDisciplineType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setSlug($dataTransformer->slugify($entity->getName()));
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'The Discipline was successfully created.');

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirect($this->generateUrl('admin_discipline_index'));
            }
            return $this->redirect($this->generateUrl('admin_discipline_edit', ['id' => $entity->getId()]));
        }

        return [
            'form' => $form->createView(),
            'entity' => $entity
        ];
    }

    /**
     * Edit an existing Discipline entity.
     *
     * @Route("/discipline/{id}", name="discipline_edit", requirements={"id": "\d+"})
     * @ParamConverter("discipline", class="App\Entity\Discipline")
     *
     * @Template("admin/disciplines/edit.html.twig")
     */
    public function edit(Request $request, Discipline $discipline, TranslatorInterface $translator, DataTransformer $dataTransformer)
    {
        $form = $this->createForm(AdminDisciplineType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discipline = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $discipline->setSlug($dataTransformer->slugify($discipline->getName()));

                $em->persist($discipline);
                $em->flush();
                $this->addFlash('success', $translator->trans('Discipline has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_discipline_index');
            }
            return $this->redirectToRoute('admin_discipline_edit', ['id' => $discipline->getId()]);
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/discipline/{action}/{id}", name="discipline_set", requirements={"id": "\d+", "action" : "remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:Discipline')->findBy(['id' => $id]);

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Discipline entity.');
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
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_discipline_index')));
    }


    /**
     * Deletes, Enables and Disables selected Pages.
     *
     * @Route("/disciplines/bulk", name="discipline_bulk")
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
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_discipline_index')));
    }

    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }
}
