<?php

declare(strict_types=1);

namespace App\Controller;

use App\Query\CraftCMS\GlobalNavigation;
use App\Query\CraftCMS\Page;
use App\Query\W3C\Healthcheck;
use App\Service\GraphQlDataFormatter;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Query\QueryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/debug")
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
     */
    public function index(string $route, QueryManager $manager): Response
    {
        // Build queries
        $manager->add('page', new Page(1, $route));

        // @todo testing, remove this
        dump($manager->get('page'));

        return $this->render('debug/page.html.twig', [
            'navigation'   => $manager->getCollection('navigation'),
            'page'         => $manager->get('page'),
        ]);
    }
}
