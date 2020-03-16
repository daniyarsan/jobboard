<?php


namespace App\Controller\Providers;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BreadcrumbProvider extends AbstractController
{

    public function show()
    {
        var_dump('show breadcrumbs');
        exit;
    }
}