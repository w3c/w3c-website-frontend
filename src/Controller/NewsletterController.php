<?php

namespace App\Controller;

use App\Query\CraftCMS\Newsletters\Listing;
use App\Query\CraftCMS\Newsletters\Collection;
use App\Query\CraftCMS\Newsletters\Entry;
use App\Query\CraftCMS\Newsletters\Filters;
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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/newsletter")
 */
class NewsletterController extends AbstractController
{
    private const LIMIT = 10;

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
        $currentPage = $request->query->get('page', 1);

        $manager->add('page', new Listing($site->siteId));
        $manager->add(
            'collection',
            new Collection(
                $router,
                $site->siteId,
                $year,
                self::LIMIT,
                $currentPage
            )
        );

        [$page, $collection, $archives] = $this->buildListing($manager, $site, $currentPage, $router, $translator);
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

        return $this->render('newsletters/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'archives'   => $archives,
        ]);
    }

    /**
     * @Route("/{year}-{month}-{day}/", requirements={"year": "\d\d\d\d", "month": "\d\d", "day": "\d\d"})
     *
     * @param QueryManager    $manager
     * @param int             $year
     * @param int             $month
     * @param int             $day
     * @param Site            $site
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function show(QueryManager $manager, int $year, int $month, int $day, Site $site): Response
    {
        $manager->add('page', new Entry($site->siteId, $year, $month, $day));

        $page = $manager->get('page');
        if (empty($page)) {
            throw $this->createNotFoundException('Newsletter not found');
        }

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
        }

        return new Response($page['fullDocumentContent']);
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
        $manager->add('filters', new Filters($router, $translator, $site->siteId));

        $collection = $manager->getCollection('collection');
        $pagination = $collection->getPagination();

        if ((empty($collection) && $currentPage !== 1) ||
            ($currentPage > $pagination->getTotalPages() && $pagination->getTotalPages() > 0)
        ) {
            return $this->redirectToRoute('app_news_index', ['page' => 1]);
        }

        $page = $manager->get('page');
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
