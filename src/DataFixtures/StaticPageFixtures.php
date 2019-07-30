<?php

namespace App\DataFixtures;

use App\Entity\StaticPage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StaticPageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $pagesData = $this->getData();

        foreach ($pagesData as $pageInfo) {
            $page = new StaticPage();

            $page->setName($pageInfo['name']);
            $page->setStatus($pageInfo['status']);
            $page->setUrl($pageInfo['url']);
            $page->setContent($pageInfo['content']);

            $manager->persist($page);
        }
        $manager->flush();
    }

    public function getData() {
        return [
            [
                'name' => 'About us',
                'status' => true,
                'url' => 'about',
                'content' => 'This is about us page...'
            ],
            [
                'name' => 'Contact us',
                'status' => true,
                'url' => 'contact',
                'content' => 'This is contact us page...'
            ],
        ];
    }
}
