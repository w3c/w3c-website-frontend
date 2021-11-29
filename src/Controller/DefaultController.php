<?php

declare(strict_types=1);

namespace App\Controller;

use App\Query\CraftCMS\Home\Page as Homepage;
use App\Query\CraftCMS\Home\RecentActivities;
use App\Query\CraftCMS\Page;
use App\Query\CraftCMS\YouMayAlsoLikeRelatedEntries;
use App\Query\W3C\Healthcheck;
use App\Query\W3C\Home\Members;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class DefaultController extends AbstractController
{

    /**
     * @Route("/debug")
     * @param QueryManager        $manager
     * @param RouterInterface     $router
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function debug(QueryManager $manager, RouterInterface $router): Response
    {
        // Add test page
        // @see https://cms-dev.w3.org/admin/entries/pages/48-w3c-mission-default?site=default
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');
        $manager->add(
            'page',
            new Page($router, $singlesBreadcrumbs['homepage'], 1, "landing-page/w3c-mission-default")
        );
        $manager->add('w3c_healthcheck', new Healthcheck());

        return $this->render('debug/test.html.twig', [
            'title'             => 'Debug page',
            'navigation'        => $manager->getCollection('navigation'),
            'navigation_cached' => $manager->isHit('navigation'),
            'w3c_available'     => $manager->getQuery('w3c_healthcheck')->isHealthy(),
            'page'              => $manager->get('page'),
            'page_cached'       => $manager->isHit('page'),
        ]);
    }

    /**
     * @Route("/", name="app_default_home")
     *
     * @param Site            $site
     * @param QueryManager    $manager
     * @param RouterInterface $router
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function home(
        Site $site,
        QueryManager $manager,
        RouterInterface $router
    ): Response {

        // If locale homepage does not exist, redirect to default homepage
        if (!$site->hasLocaleData('siteLink')) {
            return $this->redirectToRoute('app_default_home', ['_locale' => $site->getDefaultLocale()]);
        }

        $manager->add('page', new Homepage($router, $site->siteId));
        $manager->add('recent-activities', new RecentActivities($site->siteId, $router));
        $manager->add('members', new Members());

        $navigation = $manager->getCollection('navigation');

        $page    = $manager->get('page');
        $recentActivities = $manager->getCollection('recent-activities');

        if (sizeof($page['latestNewsFeaturedArticles']) < 4) {
            $page['latestNewsFeaturedArticles'] = array_merge(
                $page['latestNewsFeaturedArticles'],
                array_slice($recentActivities->getCollection(), 0, (4 - sizeof($page['latestNewsFeaturedArticles'])))
            );
        }

        // Get all members having a logo (there may be several pages)
        $members = $manager->getCollection('members');
        $memberPagination = $members->getPagination();
        $members = $members->getCollection();
        for ($i = 2; $i <= $memberPagination->getTotalPages(); $i++) {
            $manager->add('members' . $i, new Members($i));
            $members = array_merge($members, $manager->getCollection('members' . $i)->getCollection());
        }

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
            dump($recentActivities);
            dump($members);
        }

        return $this->render(
            'pages/home.html.twig',
            ['page' => $page, 'members' => $members, 'navigation' => $navigation]
        );
    }

    /**
     * @Route("/{route}/", requirements={"route"=".+"}, priority=-1)
     *
     * @param Site                $site
     * @param QueryManager        $manager
     * @param RouterInterface     $router
     * @param string $route
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function index(
        Site $site,
        QueryManager $manager,
        RouterInterface $router,
        string $route
    ): Response {
        // Build queries
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');
        $manager->add('page', new Page($router, $singlesBreadcrumbs['homepage'], $site->siteId, $route));

        // If page not found, return Error 404
        $page = $manager->get('page');
        if (empty($page)) {
            throw $this->createNotFoundException('Page not found');
        }
        $page['seo']['expiry'] = $page['expiryDate'];

        $navigation = $manager->getCollection('navigation');

        $manager->add('crosslinks', new YouMayAlsoLikeRelatedEntries($router, $site->siteId, (int)$page['id']));
        $crosslinks = $manager->get('crosslinks');

        //Only for testing purposes in dev
        $twig_variables = array(
          'navigation' => $navigation,
            'page' => $page,
            'crosslinks' => $crosslinks
        );

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($twig_variables);
        }

        $template = 'pages/default.html.twig';
        if ($page['typeHandle'] === 'landingPage' || $page['typeHandle'] === 'ecosystemsLandingPage') {
            $template = 'pages/landing.html.twig';
        }

        return $this->render($template, [
            'site'          => $site,
            'navigation'    => $navigation,
            'page'          => $page,
            'crosslinks'    => $crosslinks,
            'related_links' => array_key_exists('siblings', $page) ? $page['siblings'] : null
        ]);
    }
}
