<?php

namespace App\Service\View;

use Twig\Environment;

class MenuGenerator
{
    protected $view;

    public function __construct(Environment $environment)
    {
        $this->view = $environment;
    }

    public function profileSideMenu()
    {
        $menus = [
        ];

        return $this->view->render('dashboard/_parts/menu-side-profile.html.twig', ['menus' => $menus]);
    }

    public function companySideMenu()
    {
        $menus = [
            ['title' => 'Dashboard', 'url' => 'my_company_index', 'icon' => '', 'sub' => []],
            ['title' => 'Edit Profile', 'url' => 'my_company_settings', 'icon' => '', 'sub' => []],
            ['title' => 'My Jobs', 'url' => 'my_company_jobs', 'icon' => '', 'sub' => []],
            ['title' => 'Post New Job', 'url' => 'my_company_job_new', 'icon' => '', 'sub' => []],
            ['title' => 'Logout', 'url' => 'security_logout', 'icon' => '', 'sub' => []]
        ];

        return $this->view->render('dashboard/_parts/menu-side-company.html.twig', ['menus' => $menus]);
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
            ['title' => 'BackFill', 'url' => '', 'icon' => 'fe-file-text', 'sub' => [
                ['title' => 'Feeds', 'url' => 'admin_feeds_index'],
            ]],
            ['title' => 'Settings', 'url' => '', 'icon' => 'fe-settings', 'sub' => [
                ['title' => 'Categories', 'url' => 'admin_category_index'],
            ]]
        ];

        return $this->view->render('admin/_parts/menu-side.html.twig', ['menus' => $menus]);
    }
}