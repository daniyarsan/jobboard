<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BreadcrumbController extends AbstractController
{
    /**
     * @Route("/breadcrumb", name="breadcrumb")
     */
    public function index()
    {
        return $this->render('breadcrumb/index.html.twig', [
            'controller_name' => 'BreadcrumbController',
        ]);
    }
}
