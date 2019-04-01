<?php

namespace App\Controller;

use HttpResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/", name="main")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="_index")
     * @Template("main/index.html.twig")
     */
    public function index()
    {
        return [];
    }

    /**
     * @Route ("/show-page/{id}", name="_page_show")
     * @Template("main/page.html.twig")
     */
    public function pageAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('App:StaticPage')->findOneBy(array('id' => $id, 'status' => true));
        if (!$page) {
            throw $this->createNotFoundException();
        }

        return [
            'page' => $page,
        ];
    }

    /**
     * @Route("/page/{url}", name="_url_show")
     */
    public function fallbackAction($url)
    {
        $page = $this->getDoctrine()->getManager()->getRepository('App:StaticPage')->findOneBy(
            array(
                'url' => $url,
                'status' => true
            )
        );
        if (!empty($page)) {
            return $this->forward('App\Controller\MainController::pageAction', ['id' => $page->getId()]);
        }
//        return $this->redirect($this->generateUrl('main'));
        throw $this->createNotFoundException();
    }
}