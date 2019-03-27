<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\CompanyType;
use App\Form\ProfileType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/account/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/account/register", name="security_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $group = $request->get('group');

        if (!$group) {
            $this->get('session')->getFlashBag()->add('danger', 'Please choose user group');

            return $this->render('security/register-choose.html.twig');
        } else {
            switch ($group) {
                case 'company':
                    $entity = new Company();
                    $form = $this->createForm(CompanyType::class, $entity);
                    break;
                case 'profile':
                    $entity = new Profile();
                    $form = $this->createForm(ProfileType::class, $entity);
                    break;
            }

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $password = $passwordEncoder->encodePassword($entity->getUser(), $entity->getUser()->getPlainPassword());
                $entity->getUser()->setPassword($password);
                $entity->setRole();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($entity);
                $entityManager->flush();
                return $this->redirectToRoute('home');
            }

            return $this->render(
                'security/register.html.twig', ['form' => $form->createView()]
            );
        }
    }

    /**
     * The security layer will intercept this request
     *
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
    }

}
