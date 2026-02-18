<?php

namespace App\Controller;

use App\Query\CraftCMS\Ecosystems\Ecosystem as CraftEcosystem;
use App\Query\CraftCMS\Ecosystems\RecentActivities;
use App\Query\CraftCMS\Ecosystems\Testimonials;
use App\Query\W3C\Ecosystem\Bizdev;
use App\Query\W3C\Ecosystem\BizdevLead;
use App\Query\W3C\Ecosystem\Evangelists;
use App\Query\W3C\Ecosystem\Groups;
use App\Query\W3C\Ecosystem\Members;
use Exception;
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
    #[Route(path: '/ecosystems/{slug}/', requirements: ['slug' => '[^/]+'])]
    public function show(string $slug, Site $site, QueryManager $manager, RouterInterface $router): Response
    {
        $manager->add('page', new CraftEcosystem($router, $site->siteHandle, $slug));
        $page = $manager->get('page');

        if (empty($page)) {
            throw $this->createNotFoundException('Page not found');
        }

        $manager->add('recent-activities', new RecentActivities($site->siteHandle, $page['taxonomy-id'], $router));
        $manager->add('testimonials', new Testimonials($page['taxonomy-id'], $site));
        $manager->add('evangelists', new Evangelists($page['taxonomy-slug']));
        $manager->add('groups', new Groups($page['taxonomy-slug']));
        $manager->add('members', new Members($page['taxonomy-slug']));

        $recentActivities = $manager->get('recent-activities');
        $testimonials     = $manager->getCollection('testimonials');
        try {
            $evangelists = $manager->getCollection('evangelists');
        } catch (Exception $e) {
            $evangelists = [];
        }
        try {
            $groups = $manager->getCollection('groups');
        } catch (Exception $e) {
            $groups = [];
        }
        try {
             $members = $manager->getCollection('members');
        } catch (Exception $e) {
            $members = [];
        }

        $order = ['working group'   => 0,
                  'interest group'  => 1,
                  'business group'  => 2,
                  'community group' => 3,
                  'other'           => 4
        ];
        $groups = $groups->getCollection();

        // sort $groups by type order and then by name. If a group has no type, it will be sorted to the end of the list
        usort($groups, function ($a, $b) use ($order) {
            $aType = $a['type'] ?? 'other';
            $bType = $b['type'] ?? 'other';

            if (!array_key_exists($aType, $order)) {
                $aOrder = $order['other'];
            } else {
                $aOrder = $order[$aType];
            }

            if (!array_key_exists($bType, $order)) {
                $bOrder = $order['other'];
            } else {
                $bOrder = $order[$bType];
            }

            if ($aOrder === $bOrder) {
                return strcmp($a['name'], $b['name']);
            }

            return $aOrder - $bOrder;
        });

        // No evangelists, retrieve bizdev lead
        if (count($evangelists) == 0) {
            $manager->add('bizdev', new Bizdev());
            $bizdev = $manager->get('bizdev');
            if (isset($bizdev['_links']['lead'])) {
                $leadUrl = $bizdev['_links']['lead']['href'];
                $leadHash = explode('/', $leadUrl);
                $leadHash = $leadHash[count($leadHash) - 1];
                $manager->add('lead', new BizdevLead($leadHash));
                $evangelists[] = $manager->get('lead');
            }
        }

        $page['groups']           = $groups;
        $page['recentActivities'] = $recentActivities;
        if (sizeof($recentActivities['recentEntries']) < 2) {
            $page['recentActivities']['recentEvents'] = array_slice(
                $recentActivities['recentEvents'],
                0,
                (4 - sizeof($recentActivities['recentEntries']))
            );
        }
        if (sizeof($recentActivities['recentEvents']) < 2) {
            $page['recentActivities']['recentEntries'] = array_slice(
                $recentActivities['recentEntries'],
                0,
                (4 - sizeof($recentActivities['recentEvents']))
            );
        }
        if (sizeof($page['recentActivities']['recentEntries']) > 2
            && sizeof($page['recentActivities']['recentEvents']) > 2
        ) {
            $page['recentActivities']['recentEntries'] = array_slice($page['recentActivities']['recentEntries'], 0, 2);
            $page['recentActivities']['recentEvents']  = array_slice($page['recentActivities']['recentEvents'], 0, 2);
        }
        $page['testimonials'] = $testimonials;
        $page['members']      = $members->getCollection();
        $page['evangelists']  = $evangelists;

        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'url'    => $this->generateUrl('app_ecosystem_show', ['slug' => $slug]),
            'parent' => [
                'title'  => $singlesBreadcrumbs['ecosystems']['title'],
                'url'    => $singlesBreadcrumbs['ecosystems']['url'],
                'parent' => $singlesBreadcrumbs['homepage']
            ]
        ];
        $page['feeds'] = [
            [
                'title' => 'W3C - ' . $page['title'] . ' Ecosystem',
                'href'  => $this->generateUrl('app_feed_ecosystem', ['slug' => $slug])
            ]
        ];

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($singlesBreadcrumbs);
            dump($page);
            dump($recentActivities);
        }

        return $this->render('ecosystems/show.html.twig', [
            'site'       => $site,
            'page'       => $page,
        ]);
    }
}
