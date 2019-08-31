<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Service\Helper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoriesFixtures extends Fixture
{
    protected $container;

    /**
     * CategoriesFixtures constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        foreach ($this->getCategoriesList() as $categoryItem) {
            $category = new Category();
            $category->setName($categoryItem);
            $category->setSlug($this->slougify($categoryItem));
            $manager->persist($category);
        }

        $manager->flush();
    }

    public function getCategoriesList()
    {
        return [
            'Accounting',
            'Admin-Clerical',
            'Automotive',
            'Banking',
            'Biotech',
            'Business Development',
            'Construction',
            'Consultant',
            'Customer Service',
            'Design',
            'Distribution-Shipping',
            'Education',
            'Engineering',
            'Entry Level',
            'Executive',
            'Facilities',
            'Finance',
            'Franchise',
            'General Business',
            'General Labor',
            'Government',
            'Grocery',
            'Health Care',
            'Hospitality-Hotel',
            'Human Resources',
            'Information Technology',
            'Installation-Maint-Repair',
            'Insurance',
            'Inventory',
            'Legal',
            'Management',
            'Manufacturing',
            'Marketing',
            'Media-Journalism',
            'Nonprofit-Social Services',
            'Nurse',
            'Other',
            'Pharmaceutical',
            'Professional Services',
            'Purchasing-Procurement',
            'QA-Quality Control',
            'Real Estate',
            'Research',
            'Restaurant-Food Service',
            'Retail',
            'Sales',
            'Science',
            'Skilled Labor',
            'Strategy-Planning',
            'Supply Chain',
            'Telecommunications',
            'Training',
            'Transportation',
            'Veterinary Services',
            'Warehouse'
        ];
    }

    public function slougify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
