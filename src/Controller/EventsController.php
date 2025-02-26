<?php

namespace App\Controller;

use App\Query\CraftCMS\Events\Entry;
use App\Query\CraftCMS\Events\Filters;
use App\Query\CraftCMS\Events\Listing;
use App\Query\CraftCMS\Events\Page;
use App\Query\CraftCMS\Taxonomies\Categories;
use App\Query\CraftCMS\Taxonomies\CategoryInfo;
use App\Query\CraftCMS\Taxonomies\Tags;
use App\Service\FeedHelper;
use App\Service\IcalExporter;
use Exception;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Jean-Guilhem Rouel <jean-gui@w3.org>
 */
#[Route(path: '/events')]
class EventsController extends AbstractController
{
    private const LIMIT = 10;

    /**
     *
     * @param QueryManager        $manager
     * @param Site                $site
     * @param Request             $request
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     * @param string|null         $type
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    #[Route(path: '/')]
    #[Route(path: '/{type}/', name: 'app_events_index_type', requirements: ['type' => 'ac|conferences|happenings|talks|tpac|workshops'])]
    public function index(
        QueryManager $manager,
        Site $site,
        Request $request,
        RouterInterface $router,
        TranslatorInterface $translator,
        string $type = null
    ): Response {
        // remove empty parameters
        $params = array_filter($request->query->all(), function ($value) {
            return $value != '';
        });

        if ($request->query->get('type')) {
            return $this->redirectToRoute('app_events_index_type', $params);
        } elseif ($request->query->has('type')) { // key but no value
            return $this->redirectToRoute('app_events_index', $params);
        }

        return $this->buildListing($request, $type, null, $manager, $site, $router, $translator);
    }

    /**
     *
     * @param int                 $year
     * @param QueryManager        $manager
     * @param Site                $site
     * @param Request             $request
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     * @param string|null         $type
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    #[Route(path: '/{year}/', requirements: ['year' => '\d\d\d\d'])]
    #[Route(path: '/{type}/{year}/', name: 'app_events_archive_type', requirements: ['type' => 'ac|conferences|happenings|talks|tpac|workshops', 'year' => '\d\d\d\d'])]
    public function archive(
        int $year,
        QueryManager $manager,
        Site $site,
        Request $request,
        RouterInterface $router,
        TranslatorInterface $translator,
        string $type = null
    ): Response {
        // remove empty parameters
        $params = array_merge(
            ['year' => $year],
            array_filter($request->query->all(), function ($value) {
                return $value != '';
            })
        );

        if ($request->query->get('type')) {
            return $this->redirectToRoute('app_events_archive_type', $params);
        } elseif ($request->query->has('type')) { // key but no value
            return $this->redirectToRoute('app_events_archive', $params);
        }

        return $this->buildListing($request, $type, $year, $manager, $site, $router, $translator);
    }

    /**
     *
     * @param string          $type
     * @param int             $year
     * @param string          $slug
     * @param QueryManager    $manager
     * @param RouterInterface $router
     * @param Site            $site
     * @param IcalExporter    $icalExporter
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws Exception
     */
    #[Route(path: '/{type}/{year}/{slug}.ics', requirements: ['type' => 'ac|conferences|happenings|talks|tpac|workshops', 'year' => '\d\d\d\d'])]
    public function ical(
        string $type,
        int $year,
        string $slug,
        QueryManager $manager,
        RouterInterface $router,
        Site $site,
        IcalExporter $icalExporter
    ): Response {
        $manager->add('event-type', new CategoryInfo($site->siteHandle, 'eventType', $type));
        $eventType = $manager->get('event-type');

        if (!$eventType) {
            throw $this->createNotFoundException('Event type not found');
        }

        $manager->add('event', new Entry($site->siteHandle, $eventType, $year, $slug, $router));

        $event = $manager->get('event');
        if (empty($event)) {
            throw $this->createNotFoundException('Event not found');
        }

        $postYear = intval($event['year']);
        if ($year !== $postYear) {
            return $this->redirectToRoute('app_events_show', ['type' => $type, 'slug' => $slug, 'year' => $postYear]);
        }

        return new Response(
            $icalExporter->exportEvent($event)->serialize(),
            Response::HTTP_OK,
            ['Content-Type' => 'text/calendar;charset=UTF-8']
        );
    }

    /**
     *
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws Exception
     */
    #[Route(path: '/{type}/{year}/{slug}/', requirements: ['type' => 'ac|conferences|happenings|talks|tpac|workshops', 'year' => '\d\d\d\d'])]
    public function show(
        string $type,
        int $year,
        string $slug,
        QueryManager $manager,
        RouterInterface $router,
        Site $site,
        FeedHelper $feedHelper
    ): Response {
        $manager->add('event-type', new CategoryInfo($site->siteHandle, 'eventType', $type));
        $eventType = $manager->get('event-type');

        if (!$eventType) {
            throw $this->createNotFoundException('Event type not found');
        }

        $manager->add('page', new Entry($site->siteHandle, $eventType, $year, $slug, $router));

        $page = $manager->get('page');
        if (empty($page)) {
            throw $this->createNotFoundException('Event not found');
        }

        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['category'] = (!empty($page['categories'])) ? $page['categories'][0] : [];
        unset($page['categories']);
        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'url'    => $this->generateUrl('app_events_show', ['type' => $type, 'year' => $year, 'slug' => $slug]),
            'parent' => [
                'title'  => $year,
                'url'    => $this->generateUrl('app_events_archive_type', ['type' => $type, 'year' => $year]),
                'parent' => [
                    'title'  => $eventType['title'],
                    'url'    => $this->generateUrl('app_events_index_type', ['type' => $type]),
                    'parent' => [
                        'title'  => $singlesBreadcrumbs['events']['title'],
                        'url'    => $singlesBreadcrumbs['events']['url'],
                        'parent' => $singlesBreadcrumbs['homepage']
                    ]
                ]
            ]
        ];
        $page['ical_url'] = $this->generateUrl(
            'app_events_ical',
            ['slug' => $slug, 'type' => $eventType['slug'], 'year' => $year]
        );
        $page['feeds'] = array_merge(
            [['title' => 'W3C - Events', 'href' => $this->generateUrl('app_feed_events')]],
            $feedHelper->buildTaxonomyFeeds($page)
        );

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
            dump($singlesBreadcrumbs);
        }

        return $this->render('events/show.html.twig', [
            'site'          => $site,
            'navigation'    => $manager->getCollection('navigation'),
            'page'          => $page,
            'year'          => $year,
            'slug'          => $slug,
        ]);
    }

    /**
     * @param Request             $request
     * @param string|null         $type
     * @param                     $year
     * @param QueryManager        $manager
     * @param Site                $site
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws NotFoundHttpException
     */
    private function buildListing(
        Request $request,
        ?string $type,
        $year,
        QueryManager $manager,
        Site $site,
        RouterInterface $router,
        TranslatorInterface $translator
    ): Response {
        $currentPage  = $request->query->getInt('page', 1);
        if ($currentPage < 1) {
            throw $this->createNotFoundException();
        }
        $categorySlug = $request->query->get('category');
        $tagSlug      = $request->query->get('tag');

        $manager->add('filters', new Filters($router, $translator, $site->siteHandle, $type));
        $filters     = $manager->get('filters');
        $types       = $filters['types'];
        $eventType   = [];

        if ($type) {
            foreach ($types as $eventTypeData) {
                if ($eventTypeData['slug'] == $type) {
                    $eventType   = $eventTypeData;
                    break;
                }
            }

            if (!$eventType) {
                throw $this->createNotFoundException('Event Type not found');
            }
        }

        $category = [];
        if ($categorySlug) {
            $manager->add('categories', new Categories($site->siteHandle, 'blogCategories'));
            $categories = $manager->getCollection('categories');

            foreach ($categories as $categoryData) {
                if ($categoryData['slug'] == $categorySlug) {
                    $category = $categoryData;
                    break;
                }
            }

            if (!$category) {
                throw $this->createNotFoundException('Category not found');
            }
        }

        $tag = [];
        if ($tagSlug) {
            $manager->add('tags', new Tags($site->siteHandle, 'blogTags'));
            $tags = $manager->getCollection('tags');

            foreach ($tags as $tagData) {
                if ($tagData['slug'] == $tagSlug) {
                    $tag = $tagData;
                    break;
                }
            }

            if (!$tag) {
                throw $this->createNotFoundException('Tag not found');
            }
        }

        $manager->add('page', new Page($site->siteHandle));
        $manager->add(
            'eventsListing',
            new Listing(
                $router,
                $site->siteHandle,
                array_key_exists('id', $eventType) ? $eventType['id'] : null,
                array_key_exists('id', $category) ? $category['id'] : null,
                array_key_exists('id', $tag) ? $tag['id'] : null,
                $year,
                self::LIMIT,
                $currentPage
            )
        );

        try {
            $collection = $manager->getCollection('eventsListing');
        } catch (Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        $pagination          = $collection->getPagination();
        $categories          = $filters['categories'];
        $archives            = $filters['archives'];
        $page                = $manager->get('page');
        $page['breadcrumbs'] = $this->breadcrumbs($manager, $page, $eventType, $year);

        if ($eventType) {
            $page['lead'] = $eventType['pageLead'];
            $page['title'] = $eventType['title'];
        }

        $page['feeds'] = [['title' => 'W3C - Events', 'href' => $this->generateUrl('app_feed_events')]];
        if ($categorySlug) {
            $page['feeds'][] = [
                'title' => 'W3C - ' . $filters['categories'][$categorySlug]['title'],
                'href'  => $this->generateUrl('app_feed_category', ['slug' => $categorySlug])
            ];
        }

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
            dump($filters);
            dump($collection);
            dump($pagination);
            dump($categories);
            dump($archives);
        }

        return $this->render('events/index.html.twig', [
            'site'          => $site,
            'navigation'    => $manager->getCollection('navigation'),
            'page'          => $page,
            'type_slug'     => $type,
            'category_slug' => $categorySlug,
            'entries'       => $collection,
            'pagination'    => $collection->getPagination(),
            'categories'    => $categories,
            'archives'      => $archives,
            'types'         => $types,
            'reset_url'     => $this->generateUrl('app_events_index')
        ]);
    }

    /**
     * @param QueryManager    $manager
     * @param                 $page
     * @param                 $eventType
     * @param                 $year
     *
     * @return array          recursive breadcrumbs
     * @throws QueryManagerException
     */
    private function breadcrumbs(
        QueryManager $manager,
        $page,
        $eventType,
        $year
    ): array {
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');
        $breadcrumbs = [
            'title'  => $page['title'],
            'url'    => $this->generateUrl('app_events_index'),
            'parent' => $singlesBreadcrumbs['homepage']
        ];

        if ($eventType) {
            $breadcrumbs = [
                'title'  => $eventType['title'],
                'url'    => $this->generateUrl('app_events_index_type', ['type' => $eventType['slug']]),
                'parent' => $breadcrumbs
            ];
        }

        if ($year) {
            if ($eventType) {
                $breadcrumbs = [
                    'title'  => $year,
                    'url' => $this->generateUrl(
                        'app_events_archive_type',
                        ['year' => $year, 'type' => $eventType['slug']]
                    ),
                    'parent' => $breadcrumbs
                ];
            } else {
                $breadcrumbs = [
                    'title'  => $year,
                    'url'    => $this->generateUrl('app_events_archive', ['year' => $year]),
                    'parent' => $breadcrumbs
                ];
            }
        }

        return $breadcrumbs;
    }
}
