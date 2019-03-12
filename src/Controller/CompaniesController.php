<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\FilterCompanyType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CompaniesController extends AbstractController
{
    /**
     * @Route("/companies", name="companies_index")
     */
    public function index(Request $request, PaginatorInterface $pagination)
    {
        $filter = $this->createForm(FilterCompanyType::class, [], ['router' => $this->get('router')]);
        $filter->handleRequest($request);

        $companies = $this->getDoctrine()->getRepository('App:Company')->findByFilterQuery($request);
        $companies = $pagination->paginate($companies, $request->query->getInt('page', 1), 10);

        return $this->render(
            'companies/index.html.twig',
            [
                'companies' => $companies,
                'filter' => $filter->createView(),
                'jobRepository' => $this->getDoctrine()->getRepository('App:Job')
            ]
        );
    }

    /**
     * @Route("/company/{id}", name="company_details", requirements={"id": "\d+"})
     * @ParamConverter("company", class="App\Entity\Company")
     */
    public function companyDetails(Request $request, Company $company)
    {
        $jobs = $this->getDoctrine()->getRepository('App:Job')->findBy(
            [
                'company' => $company,
                'isPublished' => 1,
            ]
        );
        $recent_jobs = $this->getDoctrine()->getRepository('App:Job')->findRecent(4);


        return $this->render(
            'companies/company-details.html.twig',
            [
                'company' => $company,
                'jobs' => $jobs,
                'recent_jobs' => $recent_jobs
            ]
        );
    }
}
