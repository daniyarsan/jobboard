<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\CompanyType;
use App\Form\JobType;
use App\Form\UserType;
use App\Service\FileManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        return $this->render('frontend/company/index.html.twig', []);
    }

    /**
     * @Route("/settings", name="_settings")
     */
    public function settings(Request $request, TranslatorInterface $translator, FileManager $fileManager)
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
            /* Logo Upload */
            if ($logoFile = $form[ 'logo' ]->getData()) {
                $company->setLogoName($fileManager->uploadLogo($logoFile));
            }

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                $this->addFlash('success', $translator->trans('Company details has been successfully saved.'));
            } catch (\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occured when saving object.'));
            }

            return $this->redirectToRoute('my_company_settings');
        }

        return $this->render(
            'frontend/company/settings.html.twig',
            [
                'form' => $form->createView(),
                'userForm' => $userForm->createView()
            ]
        );
    }

    /**
     * @Route("/jobs", name="_jobs")
     */
    public function jobs(Request $request, PaginatorInterface $paginator)
    {
        if (!in_array('ROLE_COMPANY', $this->getUser()->getRoles())) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        $jobs = $this->getDoctrine()->getRepository('App:Job')->findUserJobs($this->getUser());
        $jobs = $paginator->paginate($jobs, $request->query->getInt('page', 1), 10);

        return $this->render(
            'frontend/company/jobs.html.twig',
            [
                'jobs' => $jobs
            ]
        );
    }

    /**
     * @Route("/job/new", name="_job_new")
     */
    public function newJob(Request $request, TranslatorInterface $translator)
    {
        $job = new Job();

        $form = $this->createForm(JobType::class, $job);

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

            return $this->redirectToRoute('my_company_jobs');
        }

        return $this->render(
            'frontend/company/job_new.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/job/edit/{id}", name="_job_edit", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function editJob(Request $request, Job $job, TranslatorInterface $translator)
    {
        $form = $this->createForm(
            JobType::class,
            $job,
            [
                'user' => $this->getUser()
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($job);
                $em->flush();
                $this->addFlash('success', $translator->trans('Job has been successfully updated.'));
            } catch (\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            return $this->redirectToRoute('my_company_jobs');
        }

        return $this->render(
            'frontend/company/job_edit.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/account/jobs/delete/{id}", name="job_delete", requirements={"id": "\d+"})
     * @ParamConverter("job", class="JobPlatform\AppBundle\Entity\Job")
     */
    public function deleteJob(Request $request, Job $job)
    {

        if (!$this->getDoctrine()->getRepository('AppBundle:Job')->hasUserJob($this->getUser(), $job)) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($job);
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('Job has been successfully deleted.'));
        } catch (\Exception $e) {
            $this->addFlash('danger', $this->get('translator')->trans('An error occurred when deleting object.'));
        }

        return $this->redirectToRoute('job_my');
    }

    /**
     * @Route("/job/publish/{id}", name="_job_publish", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function publishJob(Request $request, Job $job)
    {
        $payments = $this->getParameter('app.payments');

        if ($payments[ 'pay_for_publish' ][ 'enabled' ]) {
            $session = $request->getSession();
            if ($session->get('products')) {
                foreach ($session->get('products') as $product) {
                    if ($product[ 'type' ] == 'pay_for_publish' && $product[ 'job_id' ] == $job->getId()) {
                        $this->addFlash('danger', $this->get('translator')->trans('Product is already in cart.'));

                        return $this->redirectToRoute('job_my');
                    }
                }
            }

            $product = [
                'type' => 'pay_for_publish',
                'job_id' => $job->getId(),
                'price' => $payments[ 'pay_for_publish' ][ 'price' ],
                'duration' => $payments[ 'pay_for_publish' ][ 'duration' ],
            ];

            if ($session->has('products')) {
                $products = $session->get('products');

                array_push($products, $product);
                $session->set('products', $products);
            } else {
                $session->set('products', [$product]);
            }

            $this->addFlash(
                'success',
                $this->get('translator')->trans('Request for publishing job has been added into cart.')
            );
        } else {
            $job->setActive(true);
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($job);
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving object.'));
            }
        }

        return $this->redirectToRoute('my_company_jobs');
    }

    /**
     * @Route("/job/unpublish/{id}", name="_job_unpublish", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function unpublishJob(Request $request, Job $job)
    {
        $job->setActive(false);

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('Job has been successfully unpublished.'));
        } catch (\Exception $e) {
            $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving object.'));
        }

        return $this->redirectToRoute('my_company_jobs');
    }

    /**
     * @Route("/job/feature/{id}", name="_job_feature", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function featureJob(Request $request, Job $job)
    {
        $payments = $this->getParameter('app.payments');

        if ($payments[ 'pay_for_featured' ][ 'enabled' ]) {
            $session = $request->getSession();

            if ($session->get('products')) {
                foreach ($session->get('products') as $product) {
                    if ($product[ 'type' ] == 'pay_for_featured' && $product[ 'job_id' ] == $job->getId()) {
                        $this->addFlash('danger', $this->get('translator')->trans('Product is already in cart.'));

                        return $this->redirectToRoute('my_company_jobs');
                    }
                }
            }

            $product = [
                'type' => 'pay_for_featured',
                'job_id' => $job->getId(),
                'price' => $payments[ 'pay_for_featured' ][ 'price' ],
                'duration' => $payments[ 'pay_for_featured' ][ 'duration' ],
            ];

            if ($session->has('products')) {
                $products = $session->get('products');

                array_push($products, $product);
                $session->set('products', $products);
            } else {
                $session->set('products', [$product]);
            }

            $this->addFlash(
                'success',
                $this->get('translator')->trans('Request for featuring job has been added into cart.')
            );
        } else {
            $job->setFeatured(true);

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($job);
                $em->flush();

                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('Job has been successfully marked as featured.')
                );
            } catch (\Exception $e) {
                $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving object.'));
            }
        }

        return $this->redirectToRoute('job_my');
    }

    /**
     * @Route("/job/unfeature/{id}", name="_job_unfeature", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function unfeatureJob(Request $request, Job $job)
    {
        $job->setFeatured(false);

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();
            $this->addFlash(
                'success',
                $this->get('translator')->trans('Featured sign has been successfully removed from job.')
            );
        } catch (\Exception $e) {
            $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving object.'));
        }

        return $this->redirectToRoute('my_company_jobs');
    }

    /**
     * @Route("/candidates", name="_candidates")
     */
    public function candidates(Request $request, PaginatorInterface $paginator)
    {
        $company = $this->getUser()->getCompany();
        $em = $this->getDoctrine()->getManager();

        $applicants = $em->getRepository('App:Application')->findBy([
            'company' => $company->getId()
        ]);

        $applicants = $paginator->paginate($applicants, $request->query->getInt('page', 1), 10);

        return $this->render('frontend/company/candidates.html.twig', [
            'applicants' => $applicants
        ]);
    }

    /**
     * @Route("/password", name="_password")
     */
    public function password(Request $request, UserPasswordEncoderInterface $passwordEncoder)
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
