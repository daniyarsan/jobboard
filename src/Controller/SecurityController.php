<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Profile;
use App\Entity\User;
use App\Event\RegisteredUserEvent;
use App\Form\CompanyType;
use App\Form\ProfileType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\Helper;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        Helper $helper)
    {
        $group = $request->get('group');

        if (!$group) {
            return $this->render('security/register-choose.html.twig');
        } else {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $user->setConfirmationCode($helper->getConfirmationCode());

                switch ($group) {
                    case 'company':
                        $user->setRoles(['ROLE_COMPANY']);
                        $user->setCompany(new Company());
                        break;

                    case 'profile':
                        $user->setRoles(['ROLE_USER']);
                        $user->setProfile(new Profile());
                        break;
                }

                try {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
                }

                $userRegisteredEvent = new RegisteredUserEvent($user);
                $eventDispatcher->dispatch(RegisteredUserEvent::NAME, $userRegisteredEvent);

                //return $this->redirectToRoute('security_login');
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

    /**
     * @Route("/confirm/{code}", name="security_confirmation")
     */
    public function confirmEmail(UserRepository $userRepository, string $code)
    {
        /** @var User $user */
        $user = $userRepository->findOneBy(['confirmationCode' => $code]);

        if ($user === null) {
            return new Response('404');
        }

        $user->setIsVerified(true);
        $user->setConfirmationCode('');

        $em = $this->getDoctrine()->getManager();

        $em->flush();

        return $this->render('security/confirmation.html.twig', [
            'user' => $user,
        ]);
    }
}
