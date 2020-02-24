<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Profile;
use App\Entity\User;
use App\Event\RegisteredUserEvent;
use App\Form\UserType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public const PROFILE_MYACCOUNT = 'my_profile_settings';
    public const COMPANY_MYACCOUNT = 'my_company_settings';

    /**
     * @Route("/login", name="security_login")
     * @Template("frontend/security/login.html.twig")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return [
            'last_username' => $lastUsername,
            'error' => $error
        ];
    }

    /**
     * @Route("/register", name="security_registration")
     * @Template("frontend/security/register.html.twig")
     */
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        EventDispatcherInterface $eventDispatcher)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $formCompany = $this->createForm(UserType::class, $user);
        $formCompany->handleRequest($request);

        $group = $request->get('group');

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setConfirmationCode(sha1(rand()));
            switch ($group) {
                case 'company':
                    $user->setRoles([User::ROLE_COMPANY]);
                    $user->setCompany(new Company());
                    break;

                case 'profile':
                    $user->setRoles([User::ROLE_PROFILE]);
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
            $eventDispatcher->dispatch(RegisteredUserEvent::USER_REGISTER, $userRegisteredEvent);

            return $this->redirectToRoute('security_success');
        }

        return [
            'form' => $form->createView(),
            'formCompany' => $form->createView()
        ];
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
     * @Template("frontend/security/confirmation.html.twig")
     */
    public function confirmEmail(UserRepository $userRepository, string $code)
    {
        /** @var User $user */
        $user = $userRepository->findOneBy(['confirmationCode' => $code]);

        if ($user === null) {
            return new Response('404');
        }

        $user->setIsVerified(true);
        $user->setConfirmationCode($this->getConfirmationCode());

        $em = $this->getDoctrine()->getManager();

        $em->flush();

        return [
            'user' => $user,
        ];
    }

    /**
     * @Route("/success", name="security_success")
     * @Template("frontend/security/success.html.twig")
     */
    public function success()
    {
        return [];
    }

    /**
     * @Route("/redirect", name="security_login_redirect")
     * @Template("frontend/security/success.html.twig")
     */
    public function redirectAction()
    {
        return $this->getUser()->hasRole(User::ROLE_COMPANY)
            ? $this->redirectToRoute('my_company_index')
            : $this->redirectToRoute('my_profile_index');
    }

    public function getConfirmationCode()
    {
        $randomString = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $stringLength = strlen($randomString);
        $code = '';

        for ($i = 0; $i < $stringLength; $i++) {
            $code .= $randomString[ rand(0, $stringLength - 1) ];
        }

        return $code;
    }

}
