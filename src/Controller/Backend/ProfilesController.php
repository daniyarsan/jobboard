<?php

namespace App\Controller\Backend;

use App\Entity\Profile;
use App\Form\AdminFilterProfileType;
use App\Form\ProfileType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_profiles")
 */

class ProfilesController extends AbstractController
{
    /**
     * @Route("/profiles", name="_index")
     */
    public function index(Request $request, PaginatorInterface $pagination)
    {
        $filterForm = $this->createForm(AdminFilterProfileType::class, [], ['router' => $this->get('router')]);
        $filterForm->handleRequest($request);

        $profiles = $this->getDoctrine()->getRepository('App:Profile')->findByFilterQuery($request);
        $profiles = $pagination->paginate($profiles, $request->query->getInt('page', 1), 10);

        return $this->render(
            'admin/profiles/index.html.twig',
            [
                'filterForm' => $filterForm->createView(),
                'profiles' => $profiles
            ]
        );
    }

    /**
     * @Route("/profile/{id}", name="_details", requirements={"id": "\d+"})
     * @ParamConverter("profile", class="App\Entity\Profile")
     */
    public function details(Request $request, Profile $profile)
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($profile);
                $em->flush();
                $this->addFlash('success', $this->get('translator')->trans('Profile has been successfully updated.'));
            } catch(\Exception $e) {
                $this->addFlash('danger', $this->get('translator')->trans('An error occurred when saving object.'));
            }

            return $this->redirectToRoute(
                'admin_profiles_details',
                [
                    'id' => $profile->getId(),
                ]
            );
        }

        return $this->render(
            'admin/profiles/details.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    public function create()
    {

    }
}
