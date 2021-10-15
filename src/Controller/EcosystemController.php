<?php

namespace App\Controller;

use App\Query\CraftCMS\Ecosystems\Ecosystem as CraftEcosystem;
use App\Query\CraftCMS\Ecosystems\RecentActivities;
use App\Query\CraftCMS\Ecosystems\Testimonials;
use App\Query\W3C\Ecosystem\Evangelists;
use App\Query\W3C\Ecosystem\Groups;
use App\Query\W3C\Ecosystem\Members;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class EcosystemController extends AbstractController
{
    /**
     * @Route("/ecosystems/{slug}", requirements={"slug"=".+"})
     *
     * @param string          $slug
     * @param Site            $site
     * @param QueryManager    $manager
     * @param RouterInterface $router
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function show(string $slug, Site $site, QueryManager $manager, RouterInterface $router): Response
    {
        $manager->add('page', new CraftEcosystem($site->siteId, 'ecosystems/' . $slug));
        $page = $manager->get('page');

        if (empty($page)) {
            throw $this->createNotFoundException('Page not found');
        }

        $manager->add('recent-activities', new RecentActivities($page['taxonomy-id'], $router));
        $manager->add('testimonials', new Testimonials($page['taxonomy-id'], $site));
        $manager->add('evangelists', new Evangelists($page['taxonomy-slug']));
        $manager->add('groups', new Groups($page['taxonomy-slug']));
        $manager->add('members', new Members($page['taxonomy-slug']));

        $recentActivities = $manager->get('recent-activities');
        $testimonials     = $manager->getCollection('testimonials');
        $evangelists      = $manager->getCollection('evangelists');
        $groups           = $manager->getCollection('groups');
        $members          = $manager->getCollection('members');

        $page['seo']['expiry'] = $page['expiryDate'];
        $page['groups'] = $groups;
        $page['recent_activities'] = $recentActivities;
        $page['testimonials'] = $testimonials;
        $page['members'] = $members->getCollection();
        $page['evangelists'] = $evangelists;

        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'uri'    => $page['uri'],
            'parent' => [
                'title'  => $singlesBreadcrumbs['ecosystems']['title'],
                'uri'    => $singlesBreadcrumbs['ecosystems']['uri'],
                'parent' => null
            ]
        ];

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($singlesBreadcrumbs);
            dump($recentActivities);
            dump($page);
            dump($testimonials);
            dump($evangelists);
            dump($groups);
            dump($members);
        }

        return $this->render('ecosystems/show.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
        ]);
    }
}
