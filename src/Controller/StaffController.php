<?php

declare(strict_types=1);

namespace App\Controller;

use App\Query\CraftCMS\Staff\AlumniListing;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/staff')]
class StaffController extends AbstractController
{
    /**
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
    #[Route(path: '/alumni/')]
    public function alumni(Site $site, QueryManager $manager): Response
    {
        $manager->add('alumni-listing', new AlumniListing($site->siteHandle));

        $collection = $manager->getCollection('alumni-listing');
        $page       = $manager->get('alumni-listing', '[entry]');

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
            dump($collection);
        }

        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');
        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'url'    => $this->generateUrl('app_staff_alumni'),
            'parent' => [
                'title'  => 'Staff',
                'url'    => '/staff/',
                'parent' => $singlesBreadcrumbs['homepage']
            ]
        ];

        return $this->render('staff/alumni.html.twig', [
            'site'       => $site,
            'page'       => $page,
            'alumni'     => $collection,
        ]);
    }
}
