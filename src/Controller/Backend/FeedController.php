<?php

namespace App\Controller\Backend;

use App\Entity\Feed;
use App\Form\AdminFilterType;
use App\Form\FeedType;
use App\FeedImporter\XmlParser;
use App\Repository\FeedRepository;
use App\Repository\JobRepository;
use App\Service\View\DataTransformer;
use App\Service\XmlProcessor;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin", name="admin_feeds")
 */
class FeedController extends AbstractController
{
    /**
     * Lists all Feeds
     *
     * @Route("/feeds", name="_index")
     * @Template("admin/feeds/index.html.twig")
     */
    public function index(Request $request, Session $session, PaginatorInterface $pagination)
    {
        $filterForm = $this->createForm(AdminFilterType::class);
        $filterForm->handleRequest($request);

        $itemsPerPage = $request->query->get('itemsPerPage', 20);
        $page = $request->query->getInt('page', 1);

        if ($session->get('jobsItemsPerPage') != $itemsPerPage) {
            $session->set('jobsItemsPerPage', $itemsPerPage);
            if ($page > 1) {
                return $this->redirectToRoute('admin_feeds_index', [
                    'itemsPerPage' => $itemsPerPage,
                    'page' => 1
                ]);
            }
        }

        $paginatorOptions = [
            'defaultSortFieldName' => 'id',
            'defaultSortDirection' => 'desc'
        ];

        $entities = $this->getDoctrine()->getRepository('App:Feed')->findByFilterQuery($request);
        $entities = $pagination->paginate($entities, $page, $itemsPerPage, $paginatorOptions);

        return [
            'entities' => $entities,
            'filter_form' => $filterForm->createView(),
            'bulk_action_form' => $this->createBulkActionForm()->createView()
        ];
    }

    /**
     * Create a new Feed entity.
     *
     * @Route("/feeds/new", name="_new")
     * @Template("admin/feeds/new.html.twig")
     */

    public function new(Request $request, TranslatorInterface $translator, DataTransformer $transformer)
    {
        $feed = new Feed();

        $form = $this->createForm(FeedType::class, $feed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feed = $form->getData();
            try {
                $em = $this->getDoctrine()->getManager();

                /* Feed xml with field values */
                $xmlTextSample = $feed->getXmlText();
                $defaultMapper = XmlParser::getXmlFieldNames($xmlTextSample);
                $feed->setMapperDefault($defaultMapper);
                /* Feed xml with field values */
                $feed->setSlug($transformer->slugify($feed->getName()));
                $em->persist($feed);
                $em->flush();
                $this->addFlash('success', $translator->trans('Feed has been successfully updated.'));
            } catch (\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_feeds_index');
            }
            return $this->redirect($this->generateUrl('admin_feeds_edit', ['id' => $feed->getId()]));
        }
        return [
            'form' => $form->createView(),
            'feed' => $feed
        ];
    }

    /**
     * Deletes, Enables and Disables selected Feeds.
     *
     * @Route("/feeds/bulk", name="_bulk")
     */
    public function bulkAction(Request $request)
    {
        $form = $this->createBulkActionForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $id = array_keys($request->get('jobs'));
            $action = $request->get('action');
            return $this->set($id, $action, $request);
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_feeds_index')));
    }

    /**
     * @Route("/feeds/{id}", name="_edit", requirements={"id": "\d+"})
     * @ParamConverter("feed", class="App\Entity\Feed")
     * @Template("admin/feeds/edit.html.twig")
     */
    public function edit(Request $request, Feed $feed, TranslatorInterface $translator, DataTransformer $transformer)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(FeedType::class, $feed, ['feedId' => $feed->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feed = $form->getData();
            try {
                /* Feed xml with field values */
                $xmlTextSample = $feed->getXmlText();
                $defaultMapper = XmlParser::getXmlFieldNames($xmlTextSample);
                $feed->setMapperDefault($defaultMapper);
                /* Feed xml with field values */

                /* Add Slug */
                $feed->setSlug($transformer->slugify($feed->getName()));
                $em->persist($feed);
                $em->flush();
                $this->addFlash('success', $translator->trans('Feed has been successfully updated.'));
            } catch (\Exception $e) {
                $this->addFlash('danger', $translator->trans('An error occurred when saving object.'));
            }

            if ($form->get('saveAndExit')->isClicked()) {
                return $this->redirectToRoute('admin_feeds_index');
            }

            return $this->redirect($this->generateUrl('admin_feeds_edit', ['id' => $feed->getId()]));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/feeds/{action}/{id}", name="_set", requirements={"id": "\d+", "action" : "disable|activate|remove"})
     */
    public function set($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('App:Feed')->findBy(array('id' => $id));

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find а Feed.');
        }

        foreach ($entities as $entity) {
            switch ($action) {
                case 'remove':
                    $em->remove($entity);
                    break;
            };
        }
        try {
            $em->flush();
        } catch (\Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
        }
        return $this->redirect($request->get('return_url', $this->generateUrl('admin_feeds_index')));
    }

    /**
     * @Route("/import/{id}", name="_import", requirements={"id": "\d+"})
     * @ParamConverter("feed", class="App\Entity\Feed")
     */
    public function import(Feed $feed, JobRepository $jobRepository)
    {
        /* TODO: OPTIMIZATION Make an opportunity to load file and import from local file */
//        file_exists($this->getParameter('import.xml.dir') . '/file.xml');

        $em = $this->getDoctrine()->getManager();

        $jobRepository->deleteByFeedId($feed->getSlug());
        $xmlParser = new XmlParser($em, $feed);
        $xmlParser->parse($feed->getUrl());

        return $this->redirectToRoute('admin_feeds_index');
    }

    private function createBulkActionForm()
    {
        return $this->createFormBuilder()
            ->add('action')
            ->add('pages')
            ->getForm();
    }
}
