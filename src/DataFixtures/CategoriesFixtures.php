<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Service\Data\Categories;
use App\Service\View\DataTransformer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoriesFixtures extends Fixture implements FixtureGroupInterface
{
    protected $container;
    protected $transformer;

    /**
     * CategoriesFixtures constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container, DataTransformer $dataTransformer)
    {
        $this->container = $container;
        $this->transformer = $dataTransformer;
    }

    public function load(ObjectManager $manager)
    {

        foreach ($this->getCategoriesList() as $categoryItem) {
            $category = new Category();
            $category->setName($categoryItem);
            $category->setSlug($this->transformer->slugify($categoryItem));
            $manager->persist($category);
        }

        $manager->flush();
    }

    public function getCategoriesList()
    {
        return Categories::list();
    }

    /**
     * @inheritDoc
     */
    public static function getGroups(): array
    {
        return ['routine'];
    }
}
