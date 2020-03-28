<?php


namespace App\EventSubscriber;


use App\Repository\StaticPageRepository;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;


class RouterSubscriber implements EventSubscriberInterface
{
    protected $container;
    protected $router;
    protected $pageRepository;

    public function __construct(
        ContainerInterface $container,
        RouterInterface $router,
        StaticPageRepository $pageRepository)
    {
        $this->container = $container;
        $this->router = $router;
        $this->pageRepository = $pageRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onController'
        ];
    }

    public function onController(ControllerEvent $event)
    {
//        $controller = $event->getController();
//        $request = $event->getRequest();
//
//        if ($controller[ 0 ] instanceof ExceptionController) {
//            /* If page exists */
//            $page = $this->pageRepository->findOneBy([
//                'url' => $event->getRequest()->getPathInfo(),
//                'status' => true
//            ]);
//
//            if (!empty($page)) {
//                $request->attributes->set('_route', 'frontend_staticpage_index');
//                $request->attributes->set('_controller', 'App\\Controller\\StaticPageController:index');
//                $request->attributes->set('id', $page->getId());
//
//
//                return $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST);
//            } else {
//                throw new NotFoundHttpException('Page is not found');
//            }
//        }
    }
}


/*        if ($event->isMasterRequest()) {
            $request = new Request();
            $request->attributes->set('_controller', 'App\\Controller\\StaticPageController:index');
            $request->attributes->set('id', 1);
            $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST);
            return $response;
        }*/