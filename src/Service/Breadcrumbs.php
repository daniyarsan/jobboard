<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Breadcrumbs
{
    protected  $routes = [];

    public function add($route, $name)
    {
        $this->routes[$route] = $name;
    }

    public function getBreadcrumbs()
    {
        return $this->routes;
    }
}