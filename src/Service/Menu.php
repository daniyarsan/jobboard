<?php

namespace App\Service;

use Twig\Environment;

class Menu
{
    protected $view;

    public function __construct(Environment $environment)
    {
        $this->view = $environment;
    }
    protected  $routes = [];

    public function add($route, $name)
    {
        $this->routes[$route] = $name;
    }

    public function myProfileSide()
    {
        $menus = [
            'my_profile_index' => 'Dashboard',
            'my_profile_settings' => 'My Profile'
        ];
        return $this->view->render('my-profile/_side-menu.html.twig', ['menus' => $menus]);
    }
}