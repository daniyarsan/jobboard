<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_list")
     */
    public function index(Request $request)
    {
        $products = $this->getDoctrine()->getRepository('App:Product')->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

}
