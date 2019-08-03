<?php

namespace App\Controller;

use App\Entity\Blog;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     * @Template("blog/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('App:Blog')->createQueryBuilder('b');
        $queryBuilder->where('b.active = 1');

        $itemsPerPage = $request->query->get('itemsPerPage', 10);
        $page = $request->query->get('page', 1);

        /* Paginate */
        if ($session->get('pagesItemsPerPage') != $itemsPerPage) {
            $session->set('pagesItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('blog', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }
        $paginatorOptions = [
            'defaultSortFieldName' => 'b.created',
            'defaultSortDirection' => 'asc'
        ];
        $blogs = $paginator->paginate($queryBuilder, $page, $itemsPerPage, $paginatorOptions);
        /* Paginate */

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
