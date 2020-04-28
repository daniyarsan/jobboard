<?php

namespace App\Controller\Backend;

use App\Entity\Job;
use App\Form\AdminFilterType;
use App\Form\AdminJobType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin", name="admin_jobs")
 */

class JobsController extends AbstractController
{
    /**
     * Lists all Companies
     *
     * @Route("/jobs", name="_index")
     * @Template("admin/jobs/index.html.twig")
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
                return $this->redirectToRoute('admin_jobs_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }

        $paginatorOptions = [
            'defaultSortFieldName' => 'id',
            'defaultSortDirection' => 'desc'
        ];

        $entities = $this->getDoctrine()->getRepository('App:Job')->findByFilterQueryAdmin($request);
        $entities = $pagination->paginate($entities, $page, $itemsPerPage, $paginatorOptions);

        return [
            'entities' => $entities,
            'form' => $filterForm->createView(),
            'bulk_action_form' => $this->createBulkActionForm()->createView()
        ];
    }

    /**
     * @Route("/job/{id}", name="_edit", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     * @Template("admin/jobs/edit.html.twig")
     */
    public function edit(Request $request, Job $job, TranslatorInterface $translator)
    {
        $form = $this->createForm(AdminJobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($job);
                $em->flush();
                $this->addFlash('success', $translator->trans('Job has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_jobs_index');
            }
            return $this->redirect($this->generateUrl('admin_jobs_edit', ['id' => $job->getId()]));
        }

        return ['form' => $form->createView()];
    }

    /**
     * Create a new Job entity.
     *
     * @Route("/job/create", name="_create")
     * @Template("admin/jobs/create.html.twig")
     */

    public function create(Request $request, TranslatorInterface $translator)
    {
        $job = new Job();
        $form = $this->createForm(AdminJobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($job);
                $em->flush();
                $this->addFlash('success', $translator->trans('Job has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_jobs_index');
            }
            return $this->redirect($this->generateUrl('admin_jobs_edit', ['id' => $job->getId()]));
        }
        return [
            'form' => $form->createView(),
            'job' => $job
        ];
    }


    /**
     * @Route("/job/{action}/{id}", name="_set", requirements={"id": "\d+", "action" : "disable|activate|remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:Job')->findBy(array('id' => $id));

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Job entity.');
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
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_jobs_index')));
    }


    /**
     * Deletes, Enables and Disables selected Pages.
     *
     * @Route("/jobs/bulk", name="_bulk")
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
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_jobs_index')));
    }

    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }

}
