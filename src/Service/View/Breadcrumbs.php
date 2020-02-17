<?php

namespace App\Service\View;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Breadcrumbs
{
    protected  $breadcrumbs = [];

    public function add($breadcrumbs)
    {
        $this->breadcrumbs[] = $breadcrumbs;
    }

    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }
}