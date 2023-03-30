<?php

namespace App\Controller;

use App\Query\CraftCMS\PressReleases\Collection;
use App\Query\CraftCMS\PressReleases\Entry;
use App\Query\CraftCMS\PressReleases\Filters;
use App\Query\CraftCMS\PressReleases\Listing;
use App\Query\CraftCMS\YouMayAlsoLikeRelatedEntries;
use App\Service\FeedHelper;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\PaginationException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Jean-Guilhem Rouel <jean-gui@w3.org>
 *
 * @Route("/press-releases")
 */
class PressReleasesController extends AbstractController
{
    private const LIMIT = 10;

    /**
     * @Route("/")
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

        $manager->add('page', new Listing($site->siteHandle));
        $manager->add(
            'collection',
            new Collection(
                $router,
                $site->siteHandle,
                null,
                null,
                self::LIMIT,
                $currentPage
            )
        );

        [$page, $collection, $archives] = $this->buildListing($manager, $site, $currentPage, $router, $translator);
        $singlesBreadcrumbs  = $manager->get('singles-breadcrumbs');
        $page['breadcrumbs'] = [
            'title'  => $singlesBreadcrumbs['pressReleases']['title'],
            'url'    => $singlesBreadcrumbs['pressReleases']['url'],
            'parent' => $singlesBreadcrumbs['homepage']
        ];
        $page['feeds'] = [['title' => 'W3C - Press Releases', 'href' => $this->generateUrl('app_feed_pressreleases')]];

        return $this->render('press-releases/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'archives'   => $archives,
        ]);
    }

    /**
     * @Route("/{year}/", requirements={"year": "\d\d\d\d"})
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

        $manager->add('page', new Listing($site->siteHandle));
        $manager->add(
            'collection',
            new Collection(
                $router,
                $site->siteHandle,
                $year + 1,
                $year,
                self::LIMIT,
                $currentPage
            )
        );

        [$page, $collection, $archives] = $this->buildListing($manager, $site, $currentPage, $router, $translator);
        $singlesBreadcrumbs  = $manager->get('singles-breadcrumbs');
        $page['breadcrumbs'] = [
            'title'  => $year,
            'url'    => $this->generateUrl('app_pressreleases_archive', ['year' => $year]),
            'parent' => [
                'title'  => $singlesBreadcrumbs['pressReleases']['title'],
                'url'    => $singlesBreadcrumbs['pressReleases']['url'],
                'parent' => $singlesBreadcrumbs['homepage']
            ]
        ];
        $page['title']       = $page['title'] . ' - ' . $year;
        $page['feeds'] = [['title' => 'W3C - Press Releases', 'href' => $this->generateUrl('app_feed_pressreleases')]];

        return $this->render('press-releases/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'archives'   => $archives,
        ]);
    }

    /**
     * @Route("/{year}/{slug}/", requirements={"year": "\d\d\d\d"})
     *
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function show(
        QueryManager $manager,
        int $year,
        string $slug,
        RouterInterface $router,
        Site $site,
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

        $page['seo']['expiry'] = $page['expiryDate'];
        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'url'    => $this->generateUrl('app_pressreleases_show', ['year' => $year, 'slug' => $slug]),
            'parent' => [
                'title'  => $year,
                'url'    => $this->generateUrl('app_pressreleases_archive', ['year' => $year]),
                'parent' => [
                    'title'  => $singlesBreadcrumbs['pressReleases']['title'],
                    'url'    => $singlesBreadcrumbs['pressReleases']['url'],
                    'parent' => $singlesBreadcrumbs['homepage']
                ]
            ]
        ];
        $page['feeds'] = array_merge(
            [['title' => 'W3C - Press Releases', 'href' => $this->generateUrl('app_feed_pressreleases')]],
            $feedHelper->buildTaxonomyFeeds($page)
        );

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
            dump($crosslinks);
            dump($singlesBreadcrumbs);
        }

        return $this->render('press-releases/show.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'crosslinks' => $crosslinks,
        ]);
    }

    /**
     * @param QueryManager        $manager
     * @param Site                $site
     * @param int                 $currentPage
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return RedirectResponse|array
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    protected function buildListing(
        QueryManager $manager,
        Site $site,
        int $currentPage,
        RouterInterface $router,
        TranslatorInterface $translator
    ): array {
        $manager->add('filters', new Filters($router, $translator, $site->siteHandle));

        try {
            $collection = $manager->getCollection('collection');
        } catch (PaginationException $e) {
            throw $this->createNotFoundException();
        }

        $pagination = $collection->getPagination();

        if ((empty($collection) && $currentPage !== 1) ||
            ($currentPage > $pagination->getTotalPages() && $pagination->getTotalPages() > 0)
        ) {
            return $this->redirectToRoute('app_pressreleases_index', ['page' => 1]);
        }

        $page    = $manager->get('page');
        $filters = $manager->get('filters');
        $archives = $filters['archives'];
        $page['seo']['expiry'] = $page['expiryDate'];

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($archives);
            dump($page);
            dump($collection);
            dump($pagination);
        }

        return [$page, $collection, $archives];
    }
}
