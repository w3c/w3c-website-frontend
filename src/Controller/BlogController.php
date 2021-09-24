<?php

namespace App\Controller;

use App\Query\CraftCMS\BlogFilters;
use App\Query\CraftCMS\BlogListing;
use App\Query\CraftCMS\Taxonomies\Taxonomies;
use App\Service\CraftCMS;
use DateTimeImmutable;
use Exception;
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
     * @throws Exception
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
        $manager->add('filters', new BlogFilters($siteId));

        $collection = $manager->getCollection('blogListing');
        $pagination = $collection->getPagination();

        if (empty($collection) && $currentPage !== 1) {
            return $this->redirectToRoute('app_blog_index', ['page' => 1]);
        }

        if ($currentPage > $pagination->getTotalPages() && $pagination->getTotalPages() > 0) {
            return $this->redirectToRoute('app_blog_index', ['page' => 1]);
        }

        $page    = $manager->get('blogListing', '[entry]');
        $filters = $manager->get('filters');

        $archives = range(
            (new DateTimeImmutable($filters['first']['postDate']))->format('Y'),
            (new DateTimeImmutable($filters['last']['postDate']))->format('Y')
        );
        $categories = $filters['categories'];

        dump($archives);
        dump($page);
        dump($collection);
        dump($pagination);
        dump($filters);

        return $this->render('blog/index.html.twig', [
            'navigation' => $manager->getCollection('navigation'),
            'page' => $page,
            'entries' => $collection,
            'pagination' => $pagination,
            'categories' => $categories,
            'archives' => $archives
        ]);
    }
}
