<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\FilterJobKeywordType;
use App\Form\FilterJobType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class JobsController extends AbstractController
{
    /**
     * @Route("/jobs", name="jobs_index")
     * @Template("jobs/index.html.twig")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $filter = $this->createForm(FilterJobType::class, [], ['router' => $this->get('router')]);
        $filter->handleRequest($request);

        $filterKeyword = $this->createForm(FilterJobKeywordType::class, [], ['router' => $this->get('router')]);
        $filterKeyword->handleRequest($request);

        $jobs = $this->getDoctrine()->getRepository('App:Job')->findByFilterQuery($request);
        $jobs = $paginator->paginate($jobs, $request->query->getInt('page', 1), 10);

        return [
            'filter' => $filter->createView(),
            'filterKeyword' => $filterKeyword->createView(),
            'jobs' => $jobs
        ];
    }

    /**
     * @Route("/job/{id}", name="job_details", requirements={"id": "\d+"})
     * @ParamConverter("job", class="App\Entity\Job")
     */
    public function jobDetails(Request $request, Job $job)
    {

        return $this->render(
            'jobs/job-details.html.twig',
            [
                'job' => $job
            ]
        );
    }
}
