<?php


namespace App\EventSubscriber;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecuritySubscriber implements EventSubscriberInterface
{
    protected $container;
    protected $token;

    public function __construct(ContainerInterface $container, TokenStorageInterface $tokenStorage)
    {
        $this->container = $container;
        $this->token = $tokenStorage->getToken();
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.controller' => 'onKernelRequest'];
    }

    public function onKernelRequest($event)
    {

    }
}