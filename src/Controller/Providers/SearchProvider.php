<?php

namespace App\Controller\Providers;

use App\Repository\CategoryRepository;
use App\Repository\FieldRepository;
use App\Repository\JobRepository;
use App\Service\Data\States;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
            $filters[$key]['field'] = $field;
            $filters[$key]['options'] = $jobRepository->findWithCount($field->getFieldId());
        }

        return $this->render('frontend/_parts/filter.html.twig', [
            'filters' => $filters
        ]);
    }


}