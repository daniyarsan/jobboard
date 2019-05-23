<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Job;
use App\Form\ApplicationType;
use App\Form\FilterJobKeywordType;
use App\Form\FilterJobType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;


class JobsController extends AbstractController
{
    /**
     * @Route("/jobs", name="jobs_index")
     * @Template("jobs/index.html.twig")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $filter = $this->createForm(FilterJobType::class, [], ['router' => $this->get('router')]);
        $filter->handleRequest($request);

        $filterKeyword = $this->createForm(FilterJobKeywordType::class, [], ['router' => $this->get('router')]);
        $filterKeyword->handleRequest($request);

        $jobs = $this->getDoctrine()->getRepository('App:Job')->findByFilterQuery($request);
        $jobs = $paginator->paginate($jobs, $request->query->getInt('page', 1), 10);

        return [
            'filter' => $filter->createView(),
            'filterKeyword' => $filterKeyword->createView(),
            'jobs' => $jobs
        ];
    }

    /**
     * @Route("/job/{id}", name="job_details", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function jobDetails(Request $request, Job $job)
    {
        $form = $this->createForm(ApplicationType::class, new Application(), array(
            'action' => $this->generateUrl('job_apply', ['id' => $job->getId()]),
            'method' => 'POST',
        ));

        $hasApplication = $this->getDoctrine()->getRepository('App:Application')->findOneBy(
            [
                'user' => $this->getUser(),
                'job' => $job,
            ]
        );

        return $this->render('jobs/job-details.html.twig',
            [
                'job' => $job,
                'hasApplication' => $hasApplication,
                'applyForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/job/{id}/apply", name="job_apply", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function createAction(Request $request, Job $job, TranslatorInterface $translator)
    {
        if (!$job) {
            $this->addFlash('danger', $translator->trans('Job does not exists.'));
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        if (!$this->getUser()) {
            $this->addFlash('danger', $translator->trans('Please log in before submitting proposal.'));
            return $this->redirectToRoute('job_details', ['id' => $job->getId()]);
        }

        $form = $this->createForm(ApplicationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $application = $form->getData();
            $application->setUser($this->getUser());
            $application->setJob($job);

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($application);
                $em->flush();
                $this->addFlash('success', $translator->trans('Proposal has been successfully saved.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

        }

        return $this->redirectToRoute('job_details', ['id' => $job->getId()]);
    }

    /**
     * @Route("/account/proposals/withdraw/{id}", name="application_withdraw", requirements={"id": "\d+"})
     * @ParamConverter("job", class="JobPlatform\AppBundle\Entity\Job")
     */
    public function withdrawAction(Request $request, Job $job)
    {
        if ($job->getUser() != $this->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        // Job exists
        if (!$job) {
            $this->addFlash('danger', $this->get('translator')->trans('Job does not exists.'));

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        // User is logged in
        if (!$this->getUser()) {
            $this->addFlash('danger', $this->get('translator')->trans('Please log in before withdrawing application.'));

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $application = $this->getDoctrine()->getRepository('AppBundle:Application')->findOneBy(
            [
                'user' => $this->getUser(),
                'job' => $job,
            ]
        );

        if (!$application) {
            $this->addFlash('danger', $this->get('translator')->trans('You did not applied for this job.'));

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($application);
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('Application has been successfully withdrawn.'));
        } catch(\Exception $e) {
            $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving object'));
        }

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
