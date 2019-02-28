<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/my-account", name="my_account")
 */
class MyAccountController extends AbstractController
{
    /**
     * @Route("/", name="_index")
     */
    public function index()
    {
        return $this->render('Frontend/my_account/index.html.twig', [
            'controller_name' => 'MyAccountController',
        ]);
    }

    /**
     * @Route("/my-jobs", name="_my_jobs")
     */
    public function myJobs(Request $request, PaginatorInterface $paginator)
    {
        $jobs = $this->getDoctrine()->getRepository('App:Job')->findUserJobs($this->getUser());
        $jobs = $paginator->paginate($jobs, $request->query->getInt('page', 1), 10);

        return $this->render(
            'my_account/myjobs.html.twig',
            [
                'jobs' => $jobs
            ]
        );
    }

    public function myProfile()
    {

    }

    /**
     * @Route("/new-job", name="_new_job")
     */
    public function createJob(Request $request, TranslatorInterface $translator)
    {
        $job = new Job();

        $form = $this->createForm(
            JobType::class,
            $job,
            ['user' => $this->getUser()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job = $form->getData();
            $job->setUser($this->getUser());

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($job);
                $em->flush();
                $this->addFlash('success', $translator->trans('Job has been successfully created.'));
            } catch (\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            return $this->redirectToRoute('my_account_my_jobs');
        }

        return $this->render(
            'my_account/new.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}


/*'breadcrumbs' => [
    [
        'link' => $this->get('router')->generate('homepage'),
        'title' => $this->get('translator')->trans('Home'),
    ],
    [
        'link' => $this->get('router')->generate('job_my'),
        'title' => $this->get('translator')->trans('My Jobs'),
    ],
    ['title' => $this->get('translator')->trans('Create')],
],*/