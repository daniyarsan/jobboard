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
    public function profile(Request $request, TranslatorInterface $translator)
    {
        /*if (in_array('ROLE_USER', $this->getUser()->getRoles())) {
        } */

        $profile = $this->getUser()->getProfile();

        $form = $this->createForm(ProfileType::class, $profile);
        $userForm = $this->createForm(UserType::class, $this->getUser());

        $educations = $this->getDoctrine()->getRepository('App:Education')->findBy(
            ['profile' => $profile->getId()]
        );
        $formEducation = $this->createForm(EducationsType::class, $educations, ['method' => 'POST', 'action' => $this->generateUrl('my_profile_settings_education')]);

        $experiences = $this->getDoctrine()->getRepository('App:Experience')->findBy(
            ['profile' => $profile->getId()]
        );
        $formExperience = $this->createForm(ExperiencesType::class, $experiences, ['method' => 'POST', 'action' => $this->generateUrl('my_profile_settings_experience')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($profile);
                $em->flush();
                $this->addFlash('success', $translator->trans('Company details has been successfully saved.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occured when saving object.'));
            }

            return $this->redirectToRoute('my_profile_settings');
        }

        return $this->render(
            'my_account/profile.html.twig',
            [
                'profile' => $profile,
                'form' => $form->createView(),
                'userForm' => $userForm->createView(),
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

        return $this->redirectToRoute('my_profile_settings');
    }

    /**
     * @Route("/profile/experience", name="_profile_experience")
     */
    public function experience(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(ExperiencesType::class, [new Experience()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile = $this->getUser()->getProfile();
            $em = $this->getDoctrine()->getManager();


            $newExperiences = new ArrayCollection();
            foreach ($form->getData()['experiences'] as $experience) {
                $experience->setProfile($profile);
                $newExperiences->add($experience);
            }

            $profile->setExperiences($newExperiences);

            try {
                $em->persist($profile);
                $em->flush();
                $this->addFlash('success', $translator->trans('Experience has been successfully saved.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

        }
        return $this->redirectToRoute('my_profile_settings');
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
        return $this->redirectToRoute('my_profile_settings');
    }
}
