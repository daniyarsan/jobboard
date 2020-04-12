<?php

namespace App\Controller;

use App\Entity\Discipline;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="_index")
     */
    public function index()
    {
        return $this->json(['Welcome to API']);
    }

    /**
     * @param Request $request
     * @Route("/specialties", name="_specialties")
     *
     * @return JsonResponse
     */
    public function getSpecialtiesOptions(Request $request, CategoryRepository $categoryRepository)
    {
        $disciplineId = $request->query->get('disciplineId');

        $categories = $categoryRepository->getCategoriesByDisciplineId($disciplineId);

        $response = array();
        foreach($categories as $category){
            $response[] = array(
                "id" => $category->getId(),
                "name" => $category->getName()
            );
        }

        return $this->json($response);

    }
}
