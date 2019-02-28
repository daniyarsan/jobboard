<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Service\Helper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        foreach ($this->getCategoriesList() as $categoryItem) {
            $category = new Category();
            $category->setName($categoryItem);
            $category->setSlug(Helper::slugify($categoryItem));
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
}
