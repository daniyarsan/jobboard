<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Job;
use App\Form\ApplicationType;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * @Route("/apply", name="application")
 */
class ApplicationController extends AbstractController
{
    /**
     * @Route("/{job_id}}", name="_index")
     */
    public function index($jobId)
    {
        $applicationForm = $this->createForm(ApplicationType::class, null, [
                'method' => 'POST']
        );

        return $this->render('application/index.html.twig', [
            'form' => $applicationForm->createView(),
        ]);
    }

    /**
     * @Route("/job/{id}", name="_job", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function apply(Request $request, Job $job, TranslatorInterface $translator, FileUploader $fileUploader)
    {
        if (!$job) {
            $this->addFlash('danger', $translator->trans('Job does not exist.'));
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $form = $this->createForm(ApplicationType::class, new Application());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileUploader->setTargetDirectory($this->getParameter('resumes_dir'));
            $application = $form->getData();
            $currentUser = $this->getUser();

            try {
                $application->setCompany($job->getCompany());
                $application->setJob($job);
                $application->setName($currentUser->getProfile()->getFullName());
                $application->setEmail($currentUser->getUserName());
                $application->setResume($fileUploader->upload($form['resume']->getData()));

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
}
