<?php

namespace App\Controller\Backend;

use App\Entity\Company;
use App\Form\AdminFilterType;
use App\Form\CompanyType;
use App\Service\FileManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Admin Companies Controller
 *
 * @Route("/admin", name="admin_companies")
 */

class CompaniesController extends AbstractController
{
    /**
     * Lists all Companies
     *
     * @Route("/companies", name="_index")
     * @Template("admin/companies/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $pagination)
    {
        $filterForm = $this->createForm(AdminFilterType::class);
        $filterForm->handleRequest($request);

        $itemsPerPage = $request->query->get('itemsPerPage', 20);
        $page = $request->query->getInt('page', 1);

        if ($session->get('companiesItemsPerPage') != $itemsPerPage) {
            $session->set('companiesItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('admin_companies_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }
        $paginatorOptions = [
            'defaultSortFieldName' => 'id',
            'defaultSortDirection' => 'desc'
        ];

        /* Find entities by filter (Keywords and pagination) */

        $entites = $this->getDoctrine()->getRepository('App:Company')->findByFilterQuery($request);
        $entites = $pagination->paginate($entites, $page, $itemsPerPage, $paginatorOptions);

        return [
            'entities' => $entites,
            'filter_form' => $filterForm->createView(),
            'bulk_action_form' => $this->createBulkActionForm()->createView()
        ];
    }

    /**
     * @Route("/company/{id}", name="_edit", requirements={"id": "\d+"})
     * @ParamConverter("company", class="App\Entity\Company")
     * @Template("admin/companies/edit.html.twig")
     */
    public function edit(Request $request, Company $company, TranslatorInterface $translator, FileManager $fileManager)
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $company = $form->getData();

                /* Logo Upload */
                if ($logoFile = $form['logo']->getData()) {
                    $company->setLogoName($fileManager->uploadLogo($logoFile));
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();

                $this->addFlash('success', $translator->trans('Company has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_companies_index');
            }
            return $this->redirect($this->generateUrl('admin_companies_edit', ['id' => $company->getId()]));
        }

        return ['form' => $form->createView()];
    }

    /**
     * Create a new Company entity.
     *
     * @Route("/company/create", name="_create")
     * @Template("admin/companies/create.html.twig")
     */

    public function create(Request $request, TranslatorInterface $translator)
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                $this->addFlash('success', $translator->trans('Company has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_companies_index');
            }
            return $this->redirect($this->generateUrl('admin_companies_edit', ['id' => $company->getId()]));
        }
        return [
            'form' => $form->createView(),
            'company' => $company
        ];
    }


    /**
     * @Route("/company/{action}/{id}", name="_set", requirements={"id": "\d+", "action" : "disable|activate|remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:Company')->findBy(array('id' => $id));

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }

        foreach ($entities as $entity) {
            switch ($action) {
                case 'remove':
                    $em->remove($entity);
                    break;
                case 'disable':
                    $entity->setIsVerified(false);
                    $em->persist($entity);
                    break;
                case 'activate':
                    $entity->setIsVerified(true);
                    $em->persist($entity);
                    break;
            };
        }
        try {
            $em->flush();
        } catch (\Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_companies_index')));
    }


    /**
     * Deletes, Enables and Disables selected Pages.
     *
     * @Route("/companies/bulk", name="_bulk")
     */
    public function bulkAction(Request $request)
    {
        $form = $this->createBulkActionForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $id = array_keys($request->get('companies'));
            $action = $request->get('action');
            return $this->set($id, $action, $request);
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_companies_index')));
    }

    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }

}
