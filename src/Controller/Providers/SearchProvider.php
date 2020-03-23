<?php

namespace App\Controller\Providers;

use App\Repository\CategoryRepository;
use App\Repository\FieldRepository;
use App\Repository\JobRepository;
use App\Service\Data\States;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

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

        return $this->render('frontend/_searchProvider/search.html.twig', [
            'categories' => $categories,
            'states' => $states,
            'request' => $request
        ]);
    }

    public function filterBar($request, JobRepository $jobRepository, FieldRepository $fieldRepository)
    {
        $filterFields = [];

        $fields = $fieldRepository->findBy([
            'isSystem' => true,
            'inFilter' => true
        ]);

        foreach ($fields as $key => $field) {
            $method = 'getFilterItems' . ucfirst($field->getFieldId());
            $filterFields[$key]['field'] = $field;
            $filterFields[$key]['options'] = $jobRepository->$method();
        }

        return $this->render('frontend/_searchProvider/filter.html.twig', [
            'request' => $request,
            'filterFields' => $filterFields
        ]);
    }


}