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
     * @Route("/login", name="security_login")
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
     * @Route("/register", name="security_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $group = $request->get('group');
        $entity = new User();

        if (!$group) {
            return $this->render('security/register-choose.html.twig');
        } else {
            $form = $this->createForm(UserType::class, $entity);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $password = $passwordEncoder->encodePassword($entity, $entity->getPlainPassword());
                $entity->setPassword($password);

                switch ($group) {
                    case 'company':
                        $entity->setRoles(['ROLE_COMPANY']);
                        $entity->setCompany(new Company());
                        break;
                    case 'profile':
                        $entity->setRoles(['ROLE_USER']);
                        $entity->setProfile(new Profile());
                        break;
                }

                try {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($entity);
                    $entityManager->flush();
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
                }

                return $this->redirectToRoute('security_login');
            }

            return $this->render(
                'security/register.html.twig', ['form' => $form->createView(), 'group' => $group]
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
