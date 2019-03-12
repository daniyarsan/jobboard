<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\FilterProfileType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfilesController extends AbstractController
{
    /**
     * @Route("/profiles", name="profiles_index")
     */
    public function index(Request $request, PaginatorInterface $pagination)
    {
        $profiles = $this->getDoctrine()->getRepository('App:Profile')->findByFilterQuery($request);
        $profiles = $pagination->paginate($profiles, $request->query->getInt('page', 1), 10);

        $filter = $this->createForm(FilterProfileType::class, [], ['router' => $this->get('router')]);
        $filter->handleRequest($request);

        return $this->render(
            'profiles/index.html.twig',
            [
                'profiles' => $profiles,
                'filter' => $filter->createView()
            ]
        );
    }

    /**
     * @Route("/profile_details/{id}", name="profile_details", requirements={"id": "\d+"})
     * @ParamConverter("profile", class="App\Entity\Profile")
     */
    public function profileDetails(Request $request, Profile $profile)
    {
        return $this->render(
            'profiles/profile-deatils.html.twig',
            [
                'profile' => $profile,
                'jobs' => $this->getDoctrine()->getRepository('App:Job')->findRecent(3)
            ]
        );
    }
}
