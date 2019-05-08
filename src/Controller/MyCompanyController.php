<?php

namespace App\Controller;

use App\Entity\Education;
use App\Entity\Experience;
use App\Entity\Job;
use App\Form\CompanyType;
use App\Form\EducationsType;
use App\Form\ExperiencesType;
use App\Form\JobType;
use App\Form\ProfileType;
use App\Form\UserType;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/my-company", name="my_company")
 */
class MyCompanyController extends AbstractController
{
    /**
     * @Route("/", name="_index")
     */
    public function index()
    {
        return $this->render('my-company/index.html.twig', []);
    }


    /**
     * @Route("/settings", name="_settings")
     */
    public function settings(Request $request, TranslatorInterface $translator)
    {
        /*if (!in_array('ROLE_COMPANY', $this->getUser()->getRoles())) {
        }*/

        $company = $this->getUser()->getCompany();
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('App:Company')->find($company->getId());

        $form = $this->createForm(CompanyType::class, $company);
        $userForm = $this->createForm(UserType::class, $this->getUser());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                $this->addFlash('success', $translator->trans('Company details has been successfully saved.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occured when saving object.'));
            }

            return $this->redirectToRoute('my_company_settings');
        }

        return $this->render(
            'my-company/settings.html.twig',
            [
                'form' => $form->createView(),
                'userForm' => $userForm->createView()
            ]
        );
    }

    /**
     * @Route("/my-jobs", name="_jobs")
     */
    public function myJobs(Request $request, PaginatorInterface $paginator)
    {
        if (!in_array('ROLE_COMPANY', $this->getUser()->getRoles())) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        $jobs = $this->getDoctrine()->getRepository('App:Job')->findUserJobs($this->getUser());
        $jobs = $paginator->paginate($jobs, $request->query->getInt('page', 1), 10);

        return $this->render(
            'my-company/my-jobs.html.twig',
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
            'my-company/job-details.html.twig',
            [
                //'hasCurrentUserApplied' => $application,
                'job' => $job,
            ]
        );
    }

    /**
     * @Route("/job/new", name="_job_new")
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
            $job->setCompany($this->getUser()->getCompany());

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($job);
                $em->flush();
                $this->addFlash('success', $translator->trans('Job has been successfully created.'));
            } catch (\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            return $this->redirectToRoute('my-company_my_jobs');
        }

        return $this->render(
            'my-company/new-job.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/password", name="_password")
     * @Method("POST")
     */
    public function passwordAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $currentUser = $this->getUser();
        $userForm = $this->createForm(UserType::class, $currentUser);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $password = $passwordEncoder->encodePassword($currentUser, $currentUser->getPlainPassword());
            $currentUser->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($currentUser);
            $em->flush();
            $this->addFlash('success', 'Password has been saved successfully');
        }
        return $this->redirectToRoute('my_company_settings');
    }
}
