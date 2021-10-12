<?php

namespace App\Controller;

use App\Query\CraftCMS\Ecosystems\Ecosystem as CraftEcosystem;
use App\Query\CraftCMS\Ecosystems\Testimonials;
use App\Query\W3C\Ecosystem\Evangelists;
use App\Query\W3C\Ecosystem\Groups;
use App\Query\W3C\Ecosystem\Members;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EcosystemController extends AbstractController
{
    /**
     * @Route("/ecosystems/{slug}", requirements={"slug"=".+"})
     *
     * @param string       $slug
     * @param Site         $site
     * @param QueryManager $manager
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function show(string $slug, Site $site, QueryManager $manager, Request $request): Response
    {
        $manager->add('page', new CraftEcosystem($site->siteId, 'ecosystems/' . $slug));
        $page = $manager->get('page');

        $manager->add('testimonials', new Testimonials($page['taxonomy-id']));
        $manager->add('evangelists', new Evangelists($page['taxonomy-slug']));
        $manager->add('groups', new Groups($page['taxonomy-slug']));
        $manager->add('members', new Members($page['taxonomy-slug']));

        $testimonials = $manager->getCollection('testimonials');
        $evangelists  = $manager->getCollection('evangelists');
        $groups       = $manager->getCollection('groups');
        $members      = $manager->getCollection('members');

        $page['seo']['expiry'] = $page['expiryDate'];

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

        if ($this->getParameter('kernel.environment')) {
            dump($singlesBreadcrumbs);
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
