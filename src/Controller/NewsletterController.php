<?php

namespace App\Controller;

use App\Query\CraftCMS\Newsletters\Entry;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/newsletter")
 */
class NewsletterController extends AbstractController
{
    /**
     * @Route("/{year}-{month}-{day}/", requirements={"year": "\d\d\d\d", "month": "\d\d", "day": "\d\d"})
     *
     * @param QueryManager    $manager
     * @param int             $year
     * @param int             $month
     * @param int             $day
     * @param Site            $site
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function show(QueryManager $manager, int $year, int $month, int $day, Site $site): Response
    {
        $manager->add('page', new Entry($site->siteId, $year, $month, $day));

        $page = $manager->get('page');
        if (empty($page)) {
            throw $this->createNotFoundException('Newsletter not found');
        }

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
        }

        return new Response($page['fullDocumentContent']);
    }
}
