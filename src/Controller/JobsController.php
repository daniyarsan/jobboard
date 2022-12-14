<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Job;
use App\Form\ApplicationType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/", name="frontend_jobs")
 */
class JobsController extends AbstractController
{

    /**
     * @Route("/jobs", name="_index")
     * @Template("frontend/jobs/index.html.twig")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $limit = 10;

        $applicationForm = $this->createForm(ApplicationType::class, null, [
            'method' => 'POST']
        );

        $jobs = $this->getDoctrine()->getRepository('App:Job')->findByFilterQuery($request);
        $jobs = $paginator->paginate($jobs, $request->query->getInt('page', 1), $limit);

        return [
            'limit' => $limit,
            'jobs' => $jobs,
            'applicationForm' => $applicationForm->createView()
        ];
    }

    /**
     * @Route("/job/{id}", name="_details", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function jobDetails(Request $request, Job $job)
    {
        $form = $this->createForm(ApplicationType::class, new Application(), array(
            'action' => $this->generateUrl('application_job', ['id' => $job->getId()]),
            'method' => 'POST',
        ));

        return $this->render('frontend/jobs/job-details.html.twig',
            [
                'job' => $job,
                'applyForm' => $form->createView()
            ]
        );
    }

     /**
     * @Route("/account/proposals/withdraw/{id}", name="application_withdraw", requirements={"id": "\d+"})
     * @ParamConverter("job", class="JobPlatform\AppBundle\Entity\Job")
     */
    public function withdraw(Request $request, Job $job, TranslatorInterface $translator)
    {
        if ($job->getUser() != $this->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        // Job exists
        if (!$job) {
            $this->addFlash('danger', $translator->trans('Job does not exists.'));

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        // User is logged in
        if (!$this->getUser()) {
            $this->addFlash('danger', $translator->trans('Please log in before withdrawing application.'));

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $application = $this->getDoctrine()->getRepository('AppBundle:Application')->findOneBy(
            [
                'user' => $this->getUser(),
                'job' => $job,
            ]
        );

        if (!$application) {
            $this->addFlash('danger', $translator->trans('You did not applied for this job.'));

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($application);
            $em->flush();
            $this->addFlash('success', $translator->trans('Application has been successfully withdrawn.'));
        } catch(\Exception $e) {
            $this->addFlash('danger', $translator->trans('An error occurred when saving object'));
        }

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
