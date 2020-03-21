<?php

namespace App\Controller\Providers;

use App\Repository\CategoryRepository;
use App\Repository\FieldRepository;
use App\Repository\JobRepository;
use App\Service\Data\States;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchProvider extends AbstractController
{
    public $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function searchBar($request)
    {
        $categories = $this->categoryRepository->findAllNames();
        $states = States::list();

        return $this->render('frontend/_parts/search.html.twig', [
            'categories' => $categories,
            'states' => $states,
            'request' => $request
        ]);
    }

    public function filterBar($request, FieldRepository $fieldRepository, JobRepository $jobRepository)
    {
        $filters = [];

        $fields = $fieldRepository->findBy(['inFilter' => true]);

        foreach ($fields as $key => $field) {

            $return = array();
            $optionsFromDb = $jobRepository->findWithCount($request, $field->getFieldId());
            array_walk_recursive($optionsFromDb, function($a) use (&$return) { $return[] = $a; });

            $filters[$key]['field'] = $field;
            foreach (array_count_values($return) as $item => $count) {
                $filters[$key]['options'][] = [
                    'title' => $item,
                    'count' => $count
                ];
            }
        }

        return $this->render('frontend/_parts/filter.html.twig', [
            'request' => $request,
            'filters' => $filters
        ]);
    }


}