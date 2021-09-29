<?php

namespace App\Controller;

use App\Query\CraftCMS\Page;
use App\Query\W3C\Ecosystem\Evangelists;
use App\Query\W3C\Ecosystem\Ecosystem;
use App\Query\W3C\Ecosystem\Groups;
use App\Query\W3C\Ecosystem\Members;
use App\Service\W3C;
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
     * @param QueryManager $manager
     * @param Request      $request
     *
     * @return Response
     */
    public function show(string $slug, Site $site, QueryManager $manager, Request $request): Response
    {
        // todo: retrieve proper info from craft
        $manager->add('page', new Page($site->siteId, '/ecosystems/' . $slug));
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
