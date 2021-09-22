<?php

namespace App\Controller;

use App\Query\CraftCMS\BlogListing;
use App\Query\CraftCMS\Taxonomies\Taxonomies;
use App\Service\CraftCMS;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private const LIMIT = 1;
    /**
     * @Route("/blog/")
     *
     * @param QueryManager $manager
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function index(QueryManager $manager, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        // Build queries
        $siteId = CraftCMS::getSiteForLocale($request->getLocale());
        $manager->add(
            'blogListing',
            new BlogListing(
                $siteId,
                self::LIMIT,
                $currentPage
            )
        );
        $manager->add('categories', new Taxonomies($siteId, 'blogCategories'));

        $blogListing = $manager->get('blogListing');
        $totalPages = (int) ceil($blogListing['total'] / self::LIMIT);

        if ($currentPage > $totalPages && $totalPages > 0) {
            return $this->redirectToRoute('app_blog_index', ['page' => 1]);
        }
dump($blogListing);
        return $this->render('blog/index.html.twig', [
            'navigation' => $manager->getCollection('navigation'),
            'page' => $blogListing['entry'],
            'pagination' => ['current' => $currentPage, 'total' => $totalPages],
            'entries' => $blogListing['entries'],
            'categories' => $manager->get('categories')['categories']
        ]);
    }
}
