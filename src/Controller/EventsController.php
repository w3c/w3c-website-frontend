<?php

namespace App\Controller;

use App\Query\CraftCMS\Events\Entry;
use App\Query\CraftCMS\Events\Filters;
use App\Query\CraftCMS\Events\Listing;
use App\Query\CraftCMS\Events\Page;
use App\Query\CraftCMS\Taxonomies\Categories;
use App\Query\CraftCMS\YouMayAlsoLikeRelatedEntries;
use DateTimeImmutable;
use Exception;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     *
     * @param QueryManager    $manager
     * @param Site            $site
     * @param Request         $request
     * @param RouterInterface $router
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function index(QueryManager $manager, Site $site, Request $request, RouterInterface $router): Response
    {
        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');
        
        $manager->add('page', new Page($site->siteId));
        $manager->add(
            'eventsListing',
            new Listing(
                $router,
                $site->siteId,
                null,
                null,
                null,
                null,
                $search,
                self::LIMIT,
                $currentPage
            )
        );

        [$collection, $categories, $archives] = $this->buildListing($manager, $site, $currentPage);
        $page = $manager->get('page');
        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
        }
        $page['seo']['expiry'] = $page['expiryDate'];
        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'uri'    => $page['uri'],
            'parent' => null
        ];

        return $this->render('events/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'categories' => $categories,
            'archives'   => $archives,
            'search'     => $search
        ]);
    }

    /**
     * @Route("/{year}", requirements={"year": "\d\d\d\d"})
     *
     * @param QueryManager    $manager
     * @param int             $year
     * @param Site            $site
     * @param Request         $request
     * @param RouterInterface $router
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function archive(
        QueryManager $manager,
        int $year,
        Site $site,
        Request $request,
        RouterInterface $router
    ): Response {
        return new Response();
    }

    /**
     * @Route("/categories/{slug}", requirements={"slug": "[^/]+"})
     *
     * @param QueryManager    $manager
     * @param string          $slug
     * @param Site            $site
     * @param Request         $request
     * @param RouterInterface $router
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function category(
        QueryManager $manager,
        string $slug,
        Site $site,
        Request $request,
        RouterInterface $router
    ): Response
    {
        return new Response();
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
     * @param QueryManager $manager
     * @param Site         $site
     * @param int          $currentPage
     *
     * @return RedirectResponse|array
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws Exception
     */
    protected function buildListing(QueryManager $manager, Site $site, int $currentPage): array
    {
        $manager->add('filters', new Filters($site->siteId));

        $collection = $manager->getCollection('eventsListing');
        $pagination = $collection->getPagination();

        if (empty($collection) && $currentPage !== 1) {
            return $this->redirectToRoute('app_events_index', ['page' => 1]);
        }

        if ($currentPage > $pagination->getTotalPages() && $pagination->getTotalPages() > 0) {
            return $this->redirectToRoute('app_events_index', ['page' => 1]);
        }

        $categories = $manager->getCollection('filters', '[categories]');
        $first      = $manager->get('filters', '[first]');
        $last       = $manager->get('filters', '[last]');

        $archives = range(
            (new DateTimeImmutable($first['postDate']))->format('Y'),
            (new DateTimeImmutable($last['postDate']))->format('Y')
        );

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($archives);
            dump($collection);
            dump($pagination);
            dump($categories);
        }

        return [$collection, $categories, $archives];
    }
}
