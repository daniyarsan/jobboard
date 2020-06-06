<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="_index")
     */
    public function index()
    {

        return $this->render('admin/main/index.html.twig', [
        ]);
    }


    public function navigation()
    {

    }
}
