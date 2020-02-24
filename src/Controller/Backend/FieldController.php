<?php

namespace App\Controller\Backend;


use App\Entity\Field;
use App\Form\AdminFilterType;
use App\Form\FieldType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin", name="admin_field")
 */
class FieldController extends AbstractController
{
    /**
     * Lists all Fields
     *
     * @Route("/field", name="_index")
     * @Template("admin/field/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $pagination)
    {
        $filterForm = $this->createForm(AdminFilterType::class);
        $filterForm->handleRequest($request);

        $itemsPerPage = $request->query->get('itemsPerPage', 20);
        $page = $request->query->getInt('page', 1);

        if ($session->get('jobsItemsPerPage') != $itemsPerPage) {
            $session->set('jobsItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('admin_field_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }

        $paginatorOptions = [
            'defaultSortFieldName' => 'id',
            'defaultSortDirection' => 'desc'
        ];

        $entities = $this->getDoctrine()->getRepository('App:Field')->findByFilterQuery($request);
        $entities = $pagination->paginate($entities, $page, $itemsPerPage, $paginatorOptions);

        return [
            'entities' => $entities,
            'filter_form' => $filterForm->createView(),
            'bulk_action_form' => $this->createBulkActionForm()->createView()
        ];
    }

    /**
     * Create a new Field entity.
     *
     * @Route("/field/new", name="_new")
     * @Template("admin/field/new.html.twig")
     */

    public function new(Request $request, TranslatorInterface $translator)
    {
        $field = new Field();
        $form = $this->createForm(Field::class, $field);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $field = $form->getData();

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($field);
                $em->flush();
                $this->addFlash('success', $translator->trans('Field has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_field_index');
            }
            return $this->redirect($this->generateUrl('admin_field_edit', ['id' => $field->getId()]));
        }
        return [
            'form' => $form->createView(),
            'field' => $field
        ];
    }

    /**
     * Deletes, Enables and Disables selected Fields.
     *
     * @Route("/field/bulk", name="_bulk")
     */
    public function bulkAction(Request $request)
    {
        $form = $this->createBulkActionForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $id = array_keys($request->get('jobs'));
            $action = $request->get('action');
            return $this->set($id, $action, $request);
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_field_index')));
    }

    /**
     * @Route("/field/{id}", name="_edit", requirements={"id": "\d+"})
     * @ParamConverter("field", class="App\Entity\Field")
     * @Template("admin/field/edit.html.twig")
     */
    public function edit(Request $request, Field $field, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(FieldType::class, $field);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $field = $form->getData();
            try {
                /* Field xml with field values */
                $xmlTextSample = $field->getXmlText();
                $fields = XmlProcessor::xmlFieldValues($xmlTextSample);
                $defaultSet = array_map(function($v){
                    return (!is_null($v)) ? "" : $v;
                }, array_flip($fields));
                $field->setMapperDefault($defaultSet);

                $em->persist($field);
                $em->flush();
                $this->addFlash('success', $translator->trans('Field has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_field_index');
            }

            return $this->redirect($this->generateUrl('admin_field_edit', ['id' => $field->getId()]));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/field/{action}/{id}", name="_set", requirements={"id": "\d+", "action" : "disable|activate|remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:Field')->findBy(array('id' => $id));

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find а Field.');
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
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_field_index')));
    }


    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }
}
