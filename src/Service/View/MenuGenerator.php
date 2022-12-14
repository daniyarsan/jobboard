<?php

namespace App\Service\View;

use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class MenuGenerator
{
    protected $view;
    protected $router;

    public function __construct(Environment $environment, RouterInterface $router)
    {
        $this->view = $environment;
        $this->router = $router;
    }

    public function mainHeaderMenu()
    {
        $menus = [
            ['title' => 'Apply', 'url' => $this->router->generate('frontend_main_apply'), 'icon' => '', 'sub' => []],
            ['title' => 'Jobs', 'url' =>  $this->router->generate('frontend_jobs_index'), 'icon' => '', 'sub' => []],
            ['title' => 'Agencies', 'url' => $this->router->generate('frontend_companies_index'), 'icon' => '', 'sub' => []],
            ['title' => 'Info', 'url' => '#', 'icon' => '', 'sub' => [
                ['title' => 'What is Healthcare', 'url' => $this->router->generate('frontend_staticpage_index', ['url' => 'about'])],
                ['title' => 'FAQ', 'url' => '#'],
            ]],

            ['title' => 'Blog', 'url' => 'blog', 'icon' => '', 'sub' => []]
        ];

        return $this->view->render('frontend/_parts/main-header-menu.html.twig', ['menus' => $menus]);
    }

    public function companySideMenu()
    {
        $menus = [
            ['title' => 'Dashboard', 'url' => $this->router->generate('my_company_index'), 'icon' => '', 'sub' => []],
            ['title' => 'Edit Profile', 'url' => $this->router->generate('my_company_settings'), 'icon' => '', 'sub' => []],
            ['title' => 'My Jobs', 'url' => $this->router->generate('my_company_jobs'), 'icon' => '', 'sub' => []],
            ['title' => 'Post New Job', 'url' => $this->router->generate('my_company_job_new'), 'icon' => '', 'sub' => []],
            ['title' => 'Logout', 'url' => $this->router->generate('security_logout'), 'icon' => '', 'sub' => []]
        ];

        return $this->view->render('frontend/_parts/menu-side-company.html.twig', ['menus' => $menus]);
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
                ['title' => 'Blog', 'url' => 'admin_blog_index'],
                ['title' => 'Fields', 'url' => 'admin_field_index'],
            ]],
            ['title' => 'Feeds', 'url' => '', 'icon' => 'fe-file-text', 'sub' => [
                ['title' => 'Xml', 'url' => 'admin_feeds_index'],
            ]],
            ['title' => 'Settings', 'url' => '', 'icon' => 'fe-settings', 'sub' => [
                ['title' => 'Disciplines', 'url' => 'admin_discipline_index'],
                ['title' => 'Specialties', 'url' => 'admin_category_index'],
            ]]
        ];

        return $this->view->render('admin/_parts/menu-side.html.twig', ['menus' => $menus]);
    }

    public function profileSideMenu()
    {
        $menus = [
        ];

        return $this->view->render('dashboard/_parts/menu-side-profile.html.twig', ['menus' => $menus]);
    }
}