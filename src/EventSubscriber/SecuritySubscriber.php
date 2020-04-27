<?php


namespace App\EventSubscriber;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecuritySubscriber implements EventSubscriberInterface
{
    private $container;
    private $token;
    private $em;
    private $router;

    private $logger;
    private $log = [];

    public function __construct(
        RouterInterface $router,
        ContainerInterface $container,
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em
    )
    {
        $this->container = $container;
        $this->token = $tokenStorage->getToken();
        $this->em = $em;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.controller' => 'onKernelController'];
    }

    public function onKernelController(ControllerEvent $event)
    {
        if ($event->isMasterRequest()) {
//            $this->log['Token'] = $this->securityContext->getToken();
//            $this->log['Attributes'] = $this->securityContext->getToken()->getAttributes();
//            $this->log['Credentials'] = $this->securityContext->getToken()->getCredentials();
//            $this->log['Roles'] = $this->securityContext->getToken()->getRoles();
//            $this->log['Have Correct Role Assigned'] = in_array(
//                $this->securityContext->getToken()->getRoles(), $this->roleHierarchyRoles
//            ) ? 'Yes' : 'No';
//            $this->log['Username'] = $this->securityContext->getToken()->getUsername();
//            $this->log['Is User Authenticated'] = $this->securityContext->getToken()->isAuthenticated(
//                $this->securityContext->getToken()->getUsername()
//            );
//            $this->log['Is Logged in (Normal)'] = $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')
//                ? 'Yes' : 'No';
//            $this->log['Is Logged in (Remember Me)'] = $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')
//                ? 'Yes' : 'No';
//
//            $this->logger->info(json_encode($this->log));
        }
    }
}