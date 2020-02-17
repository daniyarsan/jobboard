<?php

namespace App\Controller;

use App\Entity\Education;
use App\Entity\Experience;
use App\Form\EducationsType;
use App\Form\ExperiencesType;
use App\Form\ProfileType;
use App\Form\UserType;
use App\Service\FileManager;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/my-profile", name="my_profile")
 */
class MyProfileController extends AbstractController
{
    /**
     * @Route("/", name="_index")
     * @Template("dashboard/profile/index.html.twig")
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        /* TODO: Create dashboard for my account */
        return [];
    }

    /**
     * @Route("/settings", name="_settings")
     * @Template("dashboard/profile/settings.html.twig")
     */
    public function settings(Request $request, TranslatorInterface $translator, FileManager $fileManager)
    {
        /*if (in_array('ROLE_USER', $this->getUser()->getRoles())) {} */

        $profile = $this->getUser()->getProfile();

        $form = $this->createForm(ProfileType::class, $profile);
        $userForm = $this->createForm(UserType::class, $this->getUser());

        $educations = $this->getDoctrine()->getRepository('App:Education')->findBy(
            ['profile' => $profile->getId()]
        );
        $educations = empty($educations) ? [new Education()] : $educations;

        $formEducation = $this->createForm(EducationsType::class, ['educations' => $educations], ['method' => 'POST', 'action' => $this->generateUrl('my_profile_education')]);

        $experiences = $this->getDoctrine()->getRepository('App:Experience')->findBy(
            ['profile' => $profile->getId()]
        );
        $experiences = empty($experiences) ? [new Experience()] : $experiences;

        $formExperience = $this->createForm(ExperiencesType::class, ['experiences' => $experiences], ['method' => 'POST', 'action' => $this->generateUrl('my_profile_experience')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /* Avatar Upload */
                if ($avatarFile = $form['avatar']->getData()) {
                    $profile->setAvatarName($fileManager->upload($this->getParameter('avatars_dir')));
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($profile);
                $em->flush();
                $this->addFlash('success', $translator->trans('Profile details has been saved successfully.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occured when saving object.'));
            }

            return $this->redirectToRoute('my_profile_settings');
        }

        return [
            'profile' => $profile,
            'form' => $form->createView(),
            'userForm' => $userForm->createView(),
            'educationForm' => $formEducation->createView(),
            'experienceForm' => $formExperience->createView()
        ];
    }

    /**
     * @Route("/education", name="_education")
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
     * @Route("/experience", name="_experience")
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
