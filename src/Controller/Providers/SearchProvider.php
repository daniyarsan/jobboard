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


    public function searchBar(JobRepository $jobRepository, CategoryRepository $categoryRepository, $request)
    {

        $disciplines = $jobRepository->getDisciplines($request);
        $categories = $categoryRepository->findAll();
        $states = $jobRepository->getStates($request);

        return $this->render('frontend/_searchProvider/search.html.twig', [
            'disciplines' => $disciplines,
            'categories' => $categories,
            'states' => $states,
            'request' => $request
        ]);
    }

    public function filterBar($request, JobRepository $jobRepository, FieldRepository $fieldRepository)
    {
        $filterFields = [];
        $size = 10;

        $fields = $fieldRepository->findBy([
            'isSystem' => true,
            'inFilter' => true
        ]);

        /* Get Filters from fields */
        foreach ($fields as $key => $field) {
            $method = 'getFilterItems' . ucfirst($field->getFieldId());
            $filterFields[$key]['field'] = $field;
            $filterFields[$key]['options'] = $jobRepository->$method($request);
        }

        return $this->render('frontend/_searchProvider/filter.html.twig', [
            'request' => $request,
            'filterFields' => $filterFields,
            'size' => $size
        ]);
    }


}