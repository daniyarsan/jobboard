<?php

namespace App\Controller\Backend;

use App\Entity\Company;
use App\Form\AdminFilterCompanyType;
use App\Form\CompanyType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_companies")
 */

class CompaniesController extends AbstractController
{
    /**
     * @Route("/companies", name="_index")
     */
    public function index(Request $request, PaginatorInterface $pagination)
    {
        $filterForm = $this->createForm(AdminFilterCompanyType::class, [], ['router' => $this->get('router')]);
        $filterForm->handleRequest($request);

        $companies = $this->getDoctrine()->getRepository('App:Company')->findByFilterQuery($request);
        $companies = $pagination->paginate($companies, $request->query->getInt('page', 1), 10);

        return $this->render(
            'admin/companies/index.html.twig',
            [
                'filterForm' => $filterForm->createView(),
                'companies' => $companies
            ]
        );
    }

    /**
     * @Route("/company/{id}", name="_details", requirements={"id": "\d+"})
     * @ParamConverter("company", class="App\Entity\Company")
     */
    public function details(Request $request, Company $company)
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans('Company has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving object.'));
            }

            return $this->redirectToRoute(
                'admin_companies_details',
                [
                    'id' => $company->getId(),
                ]
            );
        }

        return $this->render(
            'admin/companies/details.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    public function create()
    {
        
    }
}
