<?php

declare(strict_types=1);

namespace App\Controller;

use App\Query\CraftCMS\Page;
use App\Query\CraftCMS\YouMayAlsoLikeRelatedEntries;
use App\Service\CraftCMS;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/debug")
     * @param QueryManager $manager
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function debug(QueryManager $manager): Response
    {
        // Add test page
        // @see https://cms-dev.w3.org/admin/entries/pages/48-w3c-mission-default?site=default
        $manager->add('page', new Page(1, "landing-page/w3c-mission-default"));

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
     * @Route("/{route}", requirements={"route"=".+"}, defaults={"route"=""}, priority=-1)
     * @todo route priority is temporarily set to -1 as it's extremely greedy because of the {route} parameter.
     *
     * @param string       $route
     * @param QueryManager $manager
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function index(string $route, Site $site, QueryManager $manager, Request $request): Response
    {
        // Build queries
        $manager->add('page', new Page($site->siteId, $route));
        $manager->add('crosslinks', new YouMayAlsoLikeRelatedEntries($site->siteId, $route));

        // If page not found, return Error 404
        $page = $manager->get('page');
        if (empty($page)) {
            throw $this->createNotFoundException('Page not found');
        }

        $seo           = $page['seoOptions'];
        $seo['expiry'] = $page['expiryDate'];

        $navigation = $manager->getCollection('navigation');
        $crosslinks = $manager->get('crosslinks');

        dump($navigation);
        dump($page);
        dump($seo);
        dump($crosslinks);

        return $this->render('pages/default.html.twig', [
            'locale'     => $site->getLocale(),
            'textDirection' => $site->getTextDirectionHtml(),
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'crosslinks' => $crosslinks,
            'seo'        => $seo
        ]);
    }
}
