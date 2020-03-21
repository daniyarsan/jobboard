<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Field;
use App\Service\Data\Categories;
use App\Service\View\DataTransformer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FieldFixtures extends Fixture implements FixtureGroupInterface
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

        foreach ($this->getData() as $item) {
            $field = new Field();
            $field->setName($item['name']);
            $field->setFieldId($item['fieldId']);
            $field->setType($item['type']);
            $field->setListingType($item['listingType']);
            $field->setSystem(true);
            $field->setInFilter($item['inFilter']);

            $manager->persist($field);
        }

        $manager->flush();
    }

    public function getData()
    {
        return [
            [
                'name' => 'Title',
                'fieldId' => 'title',
                'listingType' => 'job',
                'type' => 'text',
                'inFilter' => false,
            ],
            [
                'name' => 'Description',
                'fieldId' => 'description',
                'listingType' => 'job',
                'type' => 'textarea',
                'inFilter' => false
            ],
            [
                'name' => 'Country',
                'fieldId' => 'country',
                'listingType' => 'job',
                'type' => 'text',
                'inFilter' => false
            ],
            [
                'name' => 'State',
                'fieldId' => 'state',
                'listingType' => 'job',
                'type' => 'text',
                'inFilter' => true
            ],
            [
                'name' => 'Categories',
                'fieldId' => 'categories',
                'listingType' => 'job',
                'type' => 'text',
                'inFilter' => true
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getGroups(): array
    {
        return ['fields'];
    }
}
