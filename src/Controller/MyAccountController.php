<?php

namespace App\Controller;

use App\Entity\Education;
use App\Entity\Job;
use App\Form\CompanyType;
use App\Form\EducationsType;
use App\Form\ExperiencesType;
use App\Form\JobType;
use App\Form\ProfileType;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @Route("/company", name="_company")
     */
    public function company(Request $request)
    {
        /*if (!in_array('ROLE_COMPANY', $this->getUser()->getRoles())) {

        }*/

        $entity = $this->getDoctrine()->getRepository('App:Company')->findOneBy(
            ['user' => $this->getUser()->getId()]
        );
        $form = $this->createForm(CompanyType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans('Company details has been successfully saved.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $this->get('translator')->trans('An error occured when saving object.'));
            }

            return $this->redirectToRoute('my_account_company');
        }

        return $this->render(
            'my_account/company.html.twig',
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
            'my_account/my-jobs.html.twig',
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
            'my_account/new-job.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
    
    /* Candidate Methods */

    /**
     * @Route("/profile", name="_profile")
     */
    public function profile(Request $request)
    {
        /*if (in_array('ROLE_USER', $this->getUser()->getRoles())) {
        } */

        $entity = $this->getDoctrine()->getRepository('App:Profile')->findOneBy(
            ['user' => $this->getUser()->getId()]
        );
        $form = $this->createForm(ProfileType::class, $entity);

        $educations = $this->getDoctrine()->getRepository('App:Education')->findBy(
            ['profile' => $this->getUser()->getProfile()->getId()]
        );
        $educations = empty($educations) ? [new Education()] : $educations;
        $formEducation = $this->createForm(EducationsType::class, ['educations' => $educations], ['method' => 'POST', 'action' => $this->generateUrl('my_account_profile_education')]);

        $experiences = $this->getDoctrine()->getRepository('App:Experience')->findBy(
            ['profile' => $this->getUser()->getProfile()->getId()]
        );
        $experiences = empty($experiences) ? [new Education()] : $experiences;
        $formExperience = $this->createForm(ExperiencesType::class, ['experiences' => $experiences], ['method' => 'POST', 'action' => $this->generateUrl('my_account_profile_experience')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans('Company details has been successfully saved.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $this->get('translator')->trans('An error occured when saving object.'));
            }

            return $this->redirectToRoute('my_account_profile');
        }

        return $this->render(
            'my_account/profile.html.twig',
            [
                'form' => $form->createView(),
                'formEducation' => $formEducation->createView(),
                'formExperience' => $formExperience->createView(),
            ]
        );
    }

    /**
     * @Route("/profile/education", name="_profile_education")
     */
    public function education(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(EducationsType::class, ['educations' => [new Education()]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile = $this->getUser()->getProfile();
            $em = $this->getDoctrine()->getManager();

            $newEducations = new ArrayCollection();
            foreach ($form->getData()['educations'] as $education) {
                $education->setProfile($profile);
                $newEducations->add($education);
            }

            $profile->setEducations($newEducations);

            try {
                $em->persist($profile);
                $em->flush();
                $this->addFlash('success', $translator->trans('Education has been successfully saved.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

        }

        return $this->redirectToRoute('my_account_profile');
    }

    /**
     * @Route("/profile/experience", name="_profile_experience")
     */
    public function experience(Request $request)
    {
        $experiences = $this->getDoctrine()->getRepository('AppBundle:Experience')->findBy(
            ['profile' => $this->getUser()->getProfile()->getId()]
        );

        $form = $this->createForm(ExperiencesType::class, ['experiences' => $experiences]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();

            $originalExperiences = new ArrayCollection();
            foreach ($user->getExperiences() as $experience) {
                $originalExperiences->add($experience);
            }

            $newExperiences = new ArrayCollection();
            foreach ($form->getData()['experiences'] as $experience) {
                $experience->setUser($user);
                $newExperiences->add($experience);
            }

            foreach ($originalExperiences as $experience) {
                if (false === $newExperiences->contains($experience)) {
                    $em->remove($experience);
                }
            }

            $user->setExperiences($newExperiences);

            try {
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans('Experience has been successfully saved.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving object.'));
            }

            return $this->redirectToRoute('profile_update_experience');
        }

        return $this->render(
            'FrontBundle::Profiles/update_experience.html.twig',
            [
                'form' => $form->createView(),
                'breadcrumbs' => [
                    [
                        'link' => $this->get('router')->generate('homepage'),
                        'title' => $this->get('translator')->trans('Home'),
                    ],
                    [
                        'link' => $this->get('router')->generate('profile_update_general'),
                        'title' => $this->get('translator')->trans('Profile'),
                    ],
                    [
                        'title' => $this->get('translator')->trans('Experience'),
                    ],
                ],
            ]
        );
    }

    /**
     * @Route("/collect", name="_collect")
     */
    public function collection(Request $request)
    {
        $data = ['values' => ['a']];

        $form = $this
            ->createFormBuilder($data)
            ->add('values', CollectionType::class, [
                'entry_type'    => TextType::class,
                'entry_options' => [
                    'label' => 'Value',
                ],
                'label'        => 'Add, move, remove values and press Submit.',
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'required'     => false,
                'attr'         => [
                    'class' => 'my-selector',
                ],
            ])
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        $form->handleRequest($request);


        return $this->render('my_account/profile.html.twig', [
            'form' => $form->createView(),
            'data' => $data,
        ]);
    }
}
