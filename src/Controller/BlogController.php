<?php

namespace App\Controller;

use App\Entity\Blog;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     * @Template("blog/index.html.twig")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $blogs = $this->getDoctrine()->getRepository('App:Blog')->findAll();
        $blogs = $paginator->paginate($blogs, $request->query->getInt('page', 1), 10);

        return [
            'blogs' => $blogs
        ];
    }

    /**
     * @Route("/blog/{slug}", name="blog_details", requirements={"id": "\d+"})
     * @ParamConverter("blog", class="App\Entity\Blog")
     * @Template("blog/details.html.twig")
     */
    public function blogDetails(Request $request, Blog $blog)
    {
        return ['blog' => $blog];
    }

}
