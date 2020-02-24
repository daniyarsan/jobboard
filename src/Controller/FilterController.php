<?php

namespace App\Controller;

use App\Form\FilterJobKeywordType;
use App\Form\FilterJobType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FilterController extends AbstractController
{
    /**
     * @Route("/sidefilter", name="filter")
     */
    public function sideFilter(Request $request)
    {
//        $filter = $this->createForm(FilterJobType::class, [], ['router' => $this->get('router')]);
//        $filter->handleRequest($request);
//
//        $filterKeyword = $this->createForm(FilterJobKeywordType::class, [], ['router' => $this->get('router')]);
//        $filterKeyword->handleRequest($request);

        return $this->render('filter/index.html.twig', [
//            'filter' => $filter->createView()
        ]);
    }
}
