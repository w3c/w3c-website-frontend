<?php

namespace App\Controller;

use App\Query\CraftCMS\Events\Entry;
use App\Query\CraftCMS\Events\Filters;
use App\Query\CraftCMS\Events\Listing;
use App\Query\CraftCMS\Events\Page;
use App\Query\CraftCMS\Taxonomies\Categories;
use App\Query\CraftCMS\Taxonomies\Tags;
use App\Query\CraftCMS\YouMayAlsoLikeRelatedEntries;
use App\Service\IcalExporter;
use DateTimeImmutable;
use Exception;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Jean-Guilhem Rouel <jean-gui@w3.org>
 *
 * @Route("/events")
 */
class EventsController extends AbstractController
{
    private const LIMIT = 10;

    /**
     * @Route("/")
     * @Route(
     *     "/{type}/",
     *     name="app_events_index_type",
     *     requirements={"type": "global|ac-meeting|tpac-meeting|workshops|talks|conferences"}
     * )
     *
     * @param string|null     $type
     * @param QueryManager    $manager
     * @param Site            $site
     * @param Request         $request
     * @param RouterInterface $router
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function index(
        QueryManager $manager,
        Site $site,
        Request $request,
        RouterInterface $router,
        string $type = null
    ): Response {
        if ($request->query->get('type')) {
            return $this->redirectToRoute('app_events_index_type', $request->query->all());
        } elseif ($request->query->has('type')) {
            // type is in the QS but has no value, redirect to app_events_index after removing this parameter
            $params = array_filter($request->query->all(), function ($key) {
                return $key != 'type';
            }, ARRAY_FILTER_USE_KEY);

            return $this->redirectToRoute('app_events_index', $params);
        }

        return $this->buildListing($request, $type, null, $manager, $site, $router);
    }

    /**
     * @Route("/{year}/", requirements={"year": "\d\d\d\d"})
     * @Route(
     *     "/{type}/{year}/",
     *     name="app_events_archive_type",
     *     requirements={
     *         "type": "global|ac-meeting|tpac-meeting|workshops|talks|conferences",
     *         "year": "\d\d\d\d"
     *     }
     * )
     *
     * @param string|null     $type
     * @param int             $year
     * @param QueryManager    $manager
     * @param Site            $site
     * @param Request         $request
     * @param RouterInterface $router
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function archive(
        int $year,
        QueryManager $manager,
        Site $site,
        Request $request,
        RouterInterface $router,
        string $type = null
    ): Response {
        if ($request->query->get('type')) {
            return $this->redirectToRoute(
                'app_events_archive_type',
                array_merge(
                    ['year' => $year],
                    $request->query->all()
                )
            );
        }

        return $this->buildListing($request, $type, $year, $manager, $site, $router);
    }

    /**
     * @Route("/{type}/{year}/{slug}.ics", requirements={"year": "\d\d\d\d"})
     *
     * @param string          $type
     * @param int             $year
     * @param string          $slug
     * @param QueryManager    $manager
     * @param RouterInterface $router
     * @param Site            $site
     * @param IcalExporter    $icalExporter
     * @param Request         $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws Exception
     */
    public function ical(
        string $type,
        int $year,
        string $slug,
        QueryManager $manager,
        RouterInterface $router,
        Site $site,
        IcalExporter $icalExporter,
        Request $request
    ): Response {
        $manager->add('event', new Entry($site->siteId, $slug, $router));

        $event = $manager->get('event');
        if (empty($event)) {
            throw $this->createNotFoundException('Event not found');
        }

        $postYear = intval((new DateTimeImmutable($event['postDate']))->format('Y'));
        if ($year !== $postYear) {
            return $this->redirectToRoute('app_events_show', ['type' => $type, 'slug' => $slug, 'year' => $postYear]);
        }

        $manager->add(
            'crosslinks',
            new YouMayAlsoLikeRelatedEntries($site->siteId, substr($request->getPathInfo(), 1))
        );

        return new Response(
            $icalExporter->exportEvent($event)->serialize(),
            Response::HTTP_OK,
            ['Content-Type' => 'text/calendar;charset=UTF-8']
        );
    }

    /**
     * @Route("/{type}/{year}/{slug}", requirements={"year": "\d\d\d\d"})
     *
     * @param string          $type
     * @param int             $year
     * @param string          $slug
     * @param QueryManager    $manager
     * @param RouterInterface $router
     * @param Site            $site
     * @param Request         $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws Exception
     */
    public function show(
        string $type,
        int $year,
        string $slug,
        QueryManager $manager,
        RouterInterface $router,
        Site $site,
        Request $request
    ): Response {
        $manager->add('page', new Entry($site->siteId, $slug, $router));

        $page = $manager->get('page');
        if (empty($page)) {
            throw $this->createNotFoundException('Page not found');
        }

        $postYear = intval((new DateTimeImmutable($page['postDate']))->format('Y'));
        if ($year !== $postYear) {
            return $this->redirectToRoute('app_events_show', ['type' => $type, 'slug' => $slug, 'year' => $postYear]);
        }

        $manager->add(
            'crosslinks',
            new YouMayAlsoLikeRelatedEntries($site->siteId, substr($request->getPathInfo(), 1))
        );


        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['seo']['expiry'] = $page['expiryDate'];
        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'uri'    => $page['uri'],
            'parent' => [
                'title'  => $year,
                'uri'    => $singlesBreadcrumbs['events']['uri'] . '/' . $year,
                'parent' => [
                    'title'  => $singlesBreadcrumbs['events']['title'],
                    'uri'    => $singlesBreadcrumbs['events']['uri'],
                    'parent' => null
                ]
            ]
        ];

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
            dump($singlesBreadcrumbs);
        }

        // @todo use events post template
        return $this->render('events/show.html.twig', [
            'site'          => $site,
            'navigation'    => $manager->getCollection('navigation'),
            'page'          => $page,
            'year'          => $year,
            'slug'          => $slug,
        ]);
    }

    /**
     * @param Request         $request
     * @param string|null     $type
     * @param                 $year
     * @param QueryManager    $manager
     * @param Site            $site
     * @param RouterInterface $router
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws Exception
     */
    private function buildListing(
        Request $request,
        ?string $type,
        $year,
        QueryManager $manager,
        Site $site,
        RouterInterface $router
    ): Response {
        $currentPage  = $request->query->get('page', 1);
        $categorySlug = $request->query->get('category');
        $tagSlug      = $request->query->get('tag');

        $manager->add('filters', new Filters($site->siteId));
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
            $manager->add('categories', new Categories($site->siteId, 'blogCategories'));
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
            $manager->add('tags', new Tags($site->siteId, 'blogTags'));
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

        $manager->add('page', new Page($site->siteId));
        $manager->add(
            'eventsListing',
            new Listing(
                $router,
                $site->siteId,
                array_key_exists('id', $eventType) ? $eventType['id'] : null,
                array_key_exists('id', $category) ? $category['id'] : null,
                array_key_exists('id', $tag) ? $tag['id'] : null,
                $year,
                self::LIMIT,
                $currentPage
            )
        );

        $collection = $manager->getCollection('eventsListing');
        $pagination = $collection->getPagination();

        if (empty($collection) && $currentPage !== 1) {
            return $this->redirectToRoute('app_events_index', ['page' => 1]);
        }

        if ($currentPage > $pagination->getTotalPages() && $pagination->getTotalPages() > 0) {
            return $this->redirectToRoute('app_events_index', ['page' => 1]);
        }

        $categories = $manager->getCollection('filters', '[categories]');


        $page = $manager->get('page');
        $first = $manager->get('filters', '[first]');
        $last = $manager->get('filters', '[last]');

        $archives = [];
        if ($first && $last) {
            $archives = range($first['year'], $last['year']);
        }

        $page['seo']['expiry'] = $page['expiryDate'];
        $page['breadcrumbs'] = $this->breadcrumbs($page, $eventType, $router, $type, $year);

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
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
            'reset_url'     => $router->generate('app_events_index')
        ]);
    }

    /**
     * @param                 $page
     * @param                 $eventType
     * @param RouterInterface $router
     * @param string|null     $type
     * @param                 $year
     *
     * @return array          recursive breadcrumbs
     */
    private function breadcrumbs($page, $eventType, RouterInterface $router, ?string $type, $year): array
    {
        $breadcrumbs = [
            'title'  => $page['title'],
            'uri'    => $page['uri'],
            'parent' => null
        ];

        if ($eventType) {
            $breadcrumbs = [
                'title'  => $eventType['title'],
                'uri'    => $router->generate('app_events_index_type', ['type' => $type]),
                'parent' => $breadcrumbs
            ];
        }

        if ($year) {
            if ($eventType) {
                $breadcrumbs = [
                    'title'  => $year,
                    'uri'    => $router->generate('app_events_archive_type', ['year' => $year, 'type' => $type]),
                    'parent' => $breadcrumbs
                ];
            } else {
                $breadcrumbs = [
                    'title'  => $year,
                    'uri'    => $router->generate('app_events_archive', ['year' => $year]),
                    'parent' => $breadcrumbs
                ];
            }
        }

        return $breadcrumbs;
    }
}
