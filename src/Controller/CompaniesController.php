<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\FilterType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="frontend_companies")
 */
class CompaniesController extends AbstractController
{
    /**
     * @Route("/companies", name="_index")
     */
    public function index(Request $request, PaginatorInterface $pagination)
    {
//        $filter = $this->createForm(FilterType::class, [], ['router' => $this->get('router')]);
//        $filter->handleRequest($request);

        $companies = $this->getDoctrine()->getRepository('App:Company')->findByFilterQuery();
        $companies = $pagination->paginate($companies, $request->query->getInt('page', 1), 10);

        return $this->render(
            'frontend/companies/index.html.twig',
            [
                'companies' => $companies,
//                'filter' => $filter->createView(),
                'jobRepository' => $this->getDoctrine()->getRepository('App:Job')
            ]
        );
    }

    /**
     * @Route("/company/{id}", name="_details", requirements={"id": "\d+"})
     * @ParamConverter("company", class="App\Entity\Company")
     */
    public function companyDetails(Request $request, Company $company)
    {
        return $this->render(
            'frontend/companies/company-details.html.twig',
            [
                'company' => $company
            ]
        );
    }
}
