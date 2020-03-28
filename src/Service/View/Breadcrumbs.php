<?php

namespace App\Service\View;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Breadcrumbs
{
    protected $view;

    public function __construct(Environment $environment)
    {
        $this->view = $environment;
    }

    public function display($template, array $items)
    {
        return $this->view->display($template, ['breadcrumbs' => $items]);
    }
}