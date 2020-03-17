<?php

namespace App\Controller\Providers;

use App\Repository\CategoryRepository;
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
        $categories = $this->categoryRepository->findAllFieldNames();
        $states = States::list();

        return $this->render('frontend/_parts/search.html.twig', [
            'categories' => $categories,
            'states' => $states,
            'request' => $request
        ]);
    }

    public function filterBar()
    {
        var_dump('filterbar');
    }
}