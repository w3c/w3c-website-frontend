<?php

declare(strict_types=1);

namespace App\Controller;

use App\Query\CraftCMS\Page;
use App\Query\CraftCMS\Staff\Alumni;
use App\Query\CraftCMS\YouMayAlsoLikeRelatedEntries;
use App\Query\W3C\Healthcheck;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/staff")
 */
class StaffController extends AbstractController
{
    /**
     * @Route("/alumni/")
     * @todo get page heading content
     * @todo fix GraphQL once Craft CMS is complete
     *
     * @param Site         $site
     * @param QueryManager $manager
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function index(Site $site, QueryManager $manager): Response
    {
        $manager->add('alumni', new Alumni($site->siteId));
        $alumni = $manager->getCollection('alumni');
        $navigation = $manager->getCollection('navigation');

        //Only for testing purposes in dev
        $twig_variables = [
            'navigation' => $navigation,
            'alumni'     => $alumni
        ];

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($twig_variables);
        }

        return $this->render('staff/alumni.html.twig', [
            'site'       => $site,
            'navigation' => $navigation,
            'page'       => [],
            'alumni'     => $alumni,
        ]);
    }
}
