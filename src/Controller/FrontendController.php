<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/", name="frontend_main")
 */
class FrontendController extends AbstractController
{

    /**
     * @Route("/", name="_index")
     * @Template("frontend/main/index.html.twig")
     */

    public function index(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findForHomepage(12);

        return [
            'categories' => $categories
        ];
    }

}