<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Discipline;
use App\Service\Data\Disciplines;
use App\Service\View\DataTransformer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DisciplineFixtures extends Fixture implements FixtureGroupInterface
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
        foreach ($this->getCategoriesList() as $item) {
            $category = new Discipline();
            $category->setName($item);
            $category->setSlug($this->transformer->slugify($item));
            $manager->persist($category);
        }

        $manager->flush();
    }

    public function getCategoriesList()
    {
        return Disciplines::list();
    }

    /**
     * @inheritDoc
     */
    public static function getGroups(): array
    {
        return ['discipline'];
    }
}
