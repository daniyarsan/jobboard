<?php

namespace App\Controller\Backend;

use App\Entity\Profile;
use App\Form\AdminFilterType;
use App\Form\ProfileType;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/admin", name="admin_profiles")
 */

class ProfilesController extends AbstractController
{
    /**
     * Lists all Proifles
     *
     * @Route("/profiles", name="_index")
     * @Method("GET")
     * @Template("admin/profiles/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $pagination)
    {
        $filterForm = $this->createForm(AdminFilterType::class);
        $filterForm->handleRequest($request);

        $itemsPerPage = $request->query->get('itemsPerPage', 20);
        $page = $request->query->getInt('page', 1);

        if ($session->get('profilesItemsPerPage') != $itemsPerPage) {
            $session->set('profilesItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('admin_profiles_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }
        $paginatorOptions = [
            'defaultSortFieldName' => 'id',
            'defaultSortDirection' => 'desc'
        ];

        $entities = $this->getDoctrine()->getRepository('App:Profile')->findByFilterQuery($request);
        $entities = $pagination->paginate($entities, $page, $itemsPerPage, $paginatorOptions);

        return [
            'entities' => $entities,
            'filter_form' => $filterForm->createView(),
            'bulk_action_form' => $this->createBulkActionForm()->createView()
        ];
    }

    /**
     * Create a new Profile entity.
     *
     * @Route("/profile/create", name="_create")
     * @Template("admin/profiles/create.html.twig")
     */
    public function create(Request $request, TranslatorInterface $translator, FileUploader $fileUploader)
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile = $form->getData();
            /* Avatar Upload */
            if ($avatarFile = $form['avatar']->getData()) {
                $fileUploader->setTargetDirectory($this->getParameter('avatars_dir'));
                $profile->setAvatarName($fileUploader->upload($avatarFile));
            }

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($profile);
                $em->flush();
                $this->addFlash('success', $translator->trans('Profile has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_profiles_index');
            }
            return $this->redirect($this->generateUrl('admin_profiles_edit', ['id' => $profile->getId()]));
        }
        return [
            'form' => $form->createView(),
            'profile' => $profile
        ];
    }

    /**
     * @Route("/profile/{id}", name="_edit", requirements={"id": "\d+"})
     * @ParamConverter("profile", class="App\Entity\Profile")
     * @Template("admin/profiles/edit.html.twig")
     */
    public function edit(Request $request, Profile $profile, TranslatorInterface $translator)
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($profile);
                $em->flush();
                $this->addFlash('success', $translator->trans('Profile has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            return ['id' => $profile->getId()];
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/profile/{action}/{id}", name="_set", requirements={"id": "\d+", "action" : "disable|activate|remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:Profile')->findBy(array('id' => $id));

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
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
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_profiles_index')));
    }


    /**
     * Deletes, Enables and Disables selected Pages.
     *
     * @Route("/profiles/bulk", name="_bulk")
     */
    public function bulkAction(Request $request)
    {
        $form = $this->createBulkActionForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $id = array_keys($request->get('profiles'));
            $action = $request->get('action');
            return $this->set($id, $action, $request);
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_profiles_index')));
    }

    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }

}
