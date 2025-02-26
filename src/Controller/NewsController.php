<?php

namespace App\Controller;

use App\Query\CraftCMS\News\Collection;
use App\Query\CraftCMS\News\Entry;
use App\Query\CraftCMS\News\Filters;
use App\Query\CraftCMS\News\Listing;
use App\Query\CraftCMS\YouMayAlsoLikeRelatedEntries;
use App\Service\FeedHelper;
use Exception;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Jean-Guilhem Rouel <jean-gui@w3.org>
 */
#[Route(path: '/news')]
class NewsController extends AbstractController
{
    private const LIMIT = 10;

    /**
     *
     * @param QueryManager        $manager
     * @param Site                $site
     * @param Request             $request
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    #[Route(path: '/')]
    public function index(
        QueryManager $manager,
        Site $site,
        Request $request,
        RouterInterface $router,
        TranslatorInterface $translator
    ): Response {
        $currentPage = $request->query->getInt('page', 1);
        if ($currentPage < 1) {
            throw $this->createNotFoundException();
        }
        $search      = $request->query->get('search');

        $manager->add('page', new Listing($site->siteHandle));
        $manager->add(
            'collection',
            new Collection(
                $router,
                $site->siteHandle,
                null,
                null,
                $site->searchPattern ? sprintf($site->searchPattern, $search) : $search,
                self::LIMIT,
                $currentPage
            )
        );

        [$page, $collection, $archives] = $this->buildListing($manager, $site, $router, $translator);
        $singlesBreadcrumbs  = $manager->get('singles-breadcrumbs');
        $page['breadcrumbs'] = [
            'title'  => $singlesBreadcrumbs['news']['title'],
            'url'    => $singlesBreadcrumbs['news']['url'],
            'parent' => $singlesBreadcrumbs['homepage']
        ];

        $page['feeds'] = [['title' => 'W3C - News', 'href' => $this->generateUrl('app_feed_news')]];

        return $this->render('news/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'archives'   => $archives,
            'search'     => $search
        ]);
    }

    /**
     *
     * @param QueryManager        $manager
     * @param int                 $year
     * @param Site                $site
     * @param Request             $request
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    #[Route(path: '/{year}/', requirements: ['year' => '\d\d\d\d'])]
    public function archive(
        QueryManager $manager,
        int $year,
        Site $site,
        Request $request,
        RouterInterface $router,
        TranslatorInterface $translator
    ): Response {
        $currentPage = $request->query->getInt('page', 1);
        if ($currentPage < 1) {
            throw $this->createNotFoundException();
        }
        $search      = $request->query->get('search');

        $manager->add('page', new Listing($site->siteHandle));
        $manager->add(
            'collection',
            new Collection(
                $router,
                $site->siteHandle,
                $year + 1,
                $year,
                $site->searchPattern ? sprintf($site->searchPattern, $search) : $search,
                self::LIMIT,
                $currentPage
            )
        );
        
        [$page, $collection, $archives] = $this->buildListing($manager, $site, $router, $translator);
        $singlesBreadcrumbs  = $manager->get('singles-breadcrumbs');
        $page['breadcrumbs'] = [
            'title'  => $year,
            'url'    => $this->generateUrl('app_news_archive', ['year' => $year]),
            'parent' => [
                'title'  => $singlesBreadcrumbs['news']['title'],
                'url'    => $singlesBreadcrumbs['news']['url'],
                'parent' => $singlesBreadcrumbs['homepage']
            ]
        ];
        $page['title']       = $page['title'] . ' - ' . $year;

        $page['feeds'] = [['title' => 'W3C - News', 'href' => $this->generateUrl('app_feed_news')]];

        return $this->render('news/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'archives'   => $archives,
            'search'     => $search
        ]);
    }

    /**
     *
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    #[Route(path: '/{year}/{slug}/', requirements: ['year' => '\d\d\d\d'])]
    public function show(
        QueryManager $manager,
        int $year,
        string $slug,
        Site $site,
        RouterInterface $router,
        FeedHelper $feedHelper
    ): Response {
        $manager->add('page', new Entry($site->siteHandle, $year, $slug, $router));

        $page = $manager->get('page');
        if (empty($page)) {
            throw $this->createNotFoundException('Page not found');
        }

        $manager->add(
            'crosslinks',
            new YouMayAlsoLikeRelatedEntries($router, $site->siteHandle, (int)$page['id'])
        );

        $crosslinks         = $manager->get('crosslinks');
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'url'    => $this->generateUrl('app_news_show', ['year' => $year, 'slug' => $slug]),
            'parent' => [
                'title'  => $year,
                'url'    => $this->generateUrl('app_news_archive', ['year' => $year]),
                'parent' => [
                    'title'  => $singlesBreadcrumbs['news']['title'],
                    'url'    => $singlesBreadcrumbs['news']['url'],
                    'parent' => $singlesBreadcrumbs['homepage']
                ]
            ]
        ];
        $page['feeds'] = array_merge(
            [['title' => 'W3C - News', 'href' => $this->generateUrl('app_feed_news')]],
            $feedHelper->buildTaxonomyFeeds($page)
        );

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
            dump($crosslinks);
            dump($singlesBreadcrumbs);
        }

        // @todo use news article template
        return $this->render('news/show.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'crosslinks' => $crosslinks,
        ]);
    }

    /**
     * @param QueryManager        $manager
     * @param Site                $site
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return array
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws NotFoundHttpException
     */
    protected function buildListing(
        QueryManager $manager,
        Site $site,
        RouterInterface $router,
        TranslatorInterface $translator
    ): array {
        $manager->add('filters', new Filters($router, $translator, $site->siteHandle));

        try {
            $collection = $manager->getCollection('collection');
        } catch (Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        $pagination = $collection->getPagination();
        $page       = $manager->get('page');
        $filters    = $manager->get('filters');
        $archives   = $filters['archives'];

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($archives);
            dump($page);
            dump($collection);
            dump($pagination);
        }

        return [$page, $collection, $archives];
    }
}
