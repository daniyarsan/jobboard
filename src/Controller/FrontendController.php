<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\CandidateType;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/apply", name="_index")
     * @Template("frontend/main/apply.html.twig")
     */
    public function apply(Request $request)
    {
        $candidate = new Profile();
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($candidate); exit;
        }
        return [
            'form' => $form->createView()
        ];
    }

}