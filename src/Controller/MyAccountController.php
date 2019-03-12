<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\CompanyType;
use App\Form\JobType;
use App\Form\ProfileType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
        return $this->render('my_account/index.html.twig', [
            'controller_name' => 'MyAccountController',
        ]);
    }

    /**
     * @Route("/profile", name="_profile")
     */
    public function profile(Request $request)
    {
        if (in_array('ROLE_USER', $this->getUser()->getRoles())) {
            $entity = $this->getDoctrine()->getRepository('App:Profile')->findOneBy(
                ['user' => $this->getUser()->getId()]
            );
            $form = $this->createForm(ProfileType::class, $entity);

        } elseif (in_array('ROLE_COMPANY', $this->getUser()->getRoles())) {
            $entity = $this->getDoctrine()->getRepository('App:Company')->findOneBy(
                ['user' => $this->getUser()->getId()]
            );
            $form = $this->createForm(CompanyType::class, $entity);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans('Settings has been successfully saved.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $this->get('translator')->trans('An error occured when saving object.'));
            }

            return $this->redirectToRoute('my_account_profile');
        }

        return $this->render(
            'my_account/profile.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/my-jobs", name="_my_jobs")
     */
    public function myJobs(Request $request, PaginatorInterface $paginator)
    {
        if (!in_array('ROLE_COMPANY', $this->getUser()->getRoles())) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        $jobs = $this->getDoctrine()->getRepository('App:Job')->findUserJobs($this->getUser());
        $jobs = $paginator->paginate($jobs, $request->query->getInt('page', 1), 10);

        return $this->render(
            'my_account/myjobs.html.twig',
            [
                'jobs' => $jobs
            ]
        );
    }

    /**
     * @Route("/job/{id}", name="_job", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function jobDetails(Request $request, Job $job)
    {
        $hasUserJob = $this->getDoctrine()->getRepository('App:Job')->hasUserJob($this->getUser(), $job);

        if (!$job->getIsPublished() && !$hasUserJob) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        /*$application = $this->getDoctrine()->getRepository('App:Application')->findOneBy(
            [
                'user' => $this->getUser(),
                'job' => $job,
            ]
        );*/

        return $this->render(
            'my_account/job-details.html.twig',
            [
                //'hasCurrentUserApplied' => $application,
                'job' => $job,
            ]
        );
    }

    /**
     * @Route("/new-job", name="_new_job")
     */
    public function createJob(Request $request, TranslatorInterface $translator)
    {
        if (!in_array('ROLE_COMPANY', $this->getUser()->getRoles())) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }
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
            $job->setCompany($this->getUser()->getCompany());

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
