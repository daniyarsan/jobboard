<?php

namespace App\Controller;

use App\Repository\StaticPageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="frontend_staticpage")
 */
class StaticPageController extends AbstractController
{
    /**
     * @Route("/{url}", name="_index")
     */
    public function index($url, StaticPageRepository $pageRepository)
    {
        $page = $pageRepository->findOneBy(['url' => $url, 'status' => true]);

        return $this->render('static_page/index.html.twig', [
            'page' => $page,
        ]);
    }
}
