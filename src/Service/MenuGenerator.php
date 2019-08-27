<?php

namespace App\Service;

use Twig\Environment;

class MenuGenerator
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

    public function adminSideMenu()
    {
        $menus = [
            ['title' => 'Dashboard', 'url' => 'admin_index', 'icon' => 'fe-airplay', 'sub' => []],
            ['title' => 'Companies', 'url' => '', 'icon' => 'fe-pocket', 'sub' => [
                ['title' => 'Companies', 'url' => 'admin_companies_index'],
                ['title' => 'Jobs', 'url' => 'admin_jobs_index']
            ]],
            ['title' => 'Profiles', 'url' => '', 'icon' => 'fe-users', 'sub' => [
                ['title' => 'Candidates', 'url' => 'admin_profiles_index'],
            ]],
            ['title' => 'Content', 'url' => '', 'icon' => 'fe-file-text', 'sub' => [
                ['title' => 'Pages', 'url' => 'admin_page_index'],
                ['title' => 'Blogs', 'url' => 'admin_blog_index'],
            ]],
            ['title' => 'Settings', 'url' => '', 'icon' => 'fe-settings', 'sub' => [
                ['title' => 'Categories', 'url' => 'admin_category_index'],
            ]]
        ];

        return $this->view->render('admin/_parts/menu-side.html.twig', ['menus' => $menus]);
    }
}