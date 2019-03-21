<?php

namespace App\Controller\Backend;

use App\Entity\Job;
use App\Form\AdminFilterJobType;
use App\Form\JobType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_jobs")
 */

class JobsController extends AbstractController
{
    /**
     * @Route("/jobs", name="_index")
     */
    public function index(Request $request, PaginatorInterface $pagination)
    {
        $filterForm = $this->createForm(AdminFilterJobType::class, [], ['router' => $this->get('router')]);
        $filterForm->handleRequest($request);

        $jobs = $this->getDoctrine()->getRepository('App:Job')->findByFilterQuery($request);
        $jobs = $pagination->paginate($jobs, $request->query->getInt('page', 1), 10);

        return $this->render(
            'admin/jobs/index.html.twig',
            [
                'filterForm' => $filterForm->createView(),
                'jobs' => $jobs
            ]
        );
    }

    /**
     * @Route("/job/{id}", name="_details", requirements={"id": "\d+"})
     * @ParamConverter("company", class="App\Entity\Job")
     */
    public function details(Request $request, Job $job)
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($job);
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans('Company has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving object.'));
            }

            return $this->redirectToRoute(
                'admin_jobs_details',
                [
                    'id' => $company->getId(),
                ]
            );
        }

        return $this->render(
            'admin/jobs/details.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    public function create()
    {

    }
}
