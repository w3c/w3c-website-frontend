<?php

namespace App\Controller;

use App\Query\CraftCMS\PressReleases\Entry;
use App\Query\CraftCMS\PressReleases\Filters;
use App\Query\CraftCMS\PressReleases\Listing;
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
 * @Route("/press-releases")
 */
class PressReleasesController extends AbstractController
{
    private const LIMIT = 10;

    /**
     * @Route("/")
     *
     * @param QueryManager $manager
     * @param Site         $site
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function index(QueryManager $manager, Site $site, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);

        $manager->add(
            'pressReleasesListing',
            new Listing(
                $site->siteId,
                null,
                null,
                self::LIMIT,
                $currentPage
            )
        );

        [$page, $collection, $archives] = $this->buildListing($manager, $site, $currentPage);
        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'uri'    => $page['uri'],
            'parent' => null
        ];

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
     * @Route("/{year}", requirements={"year": "\d\d\d\d"})
     *
     * @param QueryManager $manager
     * @param int          $year
     * @param Site         $site
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function archive(QueryManager $manager, int $year, Site $site, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);

        $manager->add(
            'pressReleasesListing',
            new Listing(
                $site->siteId,
                $year + 1,
                $year,
                self::LIMIT,
                $currentPage
            )
        );

        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        [$page, $collection, $archives] = $this->buildListing($manager, $site, $currentPage);
        $page['breadcrumbs'] = [
            'title'  => $year,
            'uri'    => $singlesBreadcrumbs['pressReleases']['uri'] . '/' . $year,
            'parent' => [
                'title'  => $singlesBreadcrumbs['pressReleases']['title'],
                'uri'    => $singlesBreadcrumbs['pressReleases']['uri'],
                'parent' => null
            ]
        ];
        $page['title']       = $page['title'] . ' - ' . $year;

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
     * @Route("/{year}/{slug}", requirements={"year": "\d\d\d\d"})
     *
     * @param QueryManager    $manager
     * @param int             $year
     * @param string          $slug
     * @param RouterInterface $router
     * @param Site            $site
     * @param Request         $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function show(
        QueryManager $manager,
        int $year,
        string $slug,
        RouterInterface $router,
        Site $site,
        Request $request
    ): Response {
        $manager->add('page', new Entry($site->siteId, $year, $slug, $router));

        $page = $manager->get('page');
        if (empty($page)) {
            throw $this->createNotFoundException('Page not found');
        }

        $manager->add(
            'crosslinks',
            new YouMayAlsoLikeRelatedEntries($site->siteId, substr($request->getPathInfo(), 1))
        );

        $crosslinks         = $manager->get('crosslinks');
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['seo']['expiry'] = $page['expiryDate'];
        $page['breadcrumbs']   = [
            'title'  => $page['title'],
            'uri'    => $page['uri'],
            'parent' => [
                'title'  => $year,
                'uri'    => $singlesBreadcrumbs['pressReleases']['uri'] . '/' . $year,
                'parent' => [
                    'title'  => $singlesBreadcrumbs['pressReleases']['title'],
                    'uri'    => $singlesBreadcrumbs['pressReleases']['uri'],
                    'parent' => null
                ]
            ]
        ];

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

        $collection = $manager->getCollection('pressReleasesListing');
        $pagination = $collection->getPagination();

        if (
            (empty($collection) && $currentPage !== 1) ||
            ($currentPage > $pagination->getTotalPages() && $pagination->getTotalPages() > 0)
        ) {
            return $this->redirectToRoute('app_pressreleases_index', ['page' => 1]);
        }

        $page  = $manager->get('pressReleasesListing', '[entry]');
        $first = $manager->get('filters', '[first]');
        $last  = $manager->get('filters', '[last]');

        $archives = [];
        if ($first && $last) {
            $archives = range(
                (new DateTimeImmutable($first['postDate']))->format('Y'),
                (new DateTimeImmutable($last['postDate']))->format('Y')
            );
        }

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
