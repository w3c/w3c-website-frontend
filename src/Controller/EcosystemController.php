<?php

namespace App\Controller;

use App\Query\CraftCMS\Ecosystem as CraftEcosystem;
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
        // todo: retrieve proper info from craft
        $manager->add('page', new CraftEcosystem($site->siteId, 'ecosystems/' . $slug));
        $manager->add('evangelists', new Evangelists($slug));
        $manager->add('groups', new Groups($slug));
        $manager->add('members', new Members($slug));

        $evangelists = $manager->getCollection('evangelists');
        $groups = $manager->getCollection('groups');
        $members = $manager->getCollection('members');

        dump($evangelists);
        dump($groups);
        dump($members);

        return $this->render('pages/default.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page' => $manager->get('page')
        ]);
    }
}
