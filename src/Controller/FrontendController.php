<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\CandidateType;
use App\Repository\CategoryRepository;
use App\Repository\DisciplineRepository;
use App\Repository\JobRepository;
use App\Service\FileManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/", name="frontend_main")
 */
class FrontendController extends AbstractController
{

    /**
     * @Route("/", name="_index")
     * @Template("frontend/main/index.html.twig")
     */
    public function index(
        DisciplineRepository $disciplineRepository,
        JobRepository $jobRepository)
    {
        $recentJobs = $jobRepository->findBy([],['id'=>'DESC'],10,0);
        $totalJobsCount = $jobRepository->getTotal();

        $disciplines = $jobRepository->findForHomepage();
//        shuffle($disciplines);

        return [
            'disciplines' => array_slice($disciplines, 0, 12),
            'recentJobs' => $recentJobs,
            'totalJobsCount' => $totalJobsCount
        ];
    }

    /**
     * @Route("/apply", name="_apply")
     * @Template("frontend/main/apply.html.twig")
     */
    public function apply(Request $request,
                          ManagerRegistry $managerRegistry,
                          UserPasswordEncoderInterface $passwordEncoder, FileManager $fileManager)
    {
        $em = $managerRegistry->getManager();

        $candidate = new Profile();
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();
            $user->setEmail($candidate->getEmail());
            $user->setRoles([User::ROLE_PROFILE]);
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setProfile($candidate);
            $candidate->setUser($user);

            if ($resume = $form[ 'resume' ]->getData()) {
                $candidate->setResumeFile($fileManager->uploadResume($resume));
            }

            $em->persist($candidate);
            $em->flush();

            return $this->redirectToRoute('frontend_jobs_index');
        }
        return [
            'form' => $form->createView()
        ];
    }

}