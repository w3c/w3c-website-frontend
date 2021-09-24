<?php

namespace App\Controller;

use App\Query\CraftCMS\BlogFilters;
use App\Query\CraftCMS\BlogListing;
use App\Query\CraftCMS\Taxonomies\Categories;
use App\Query\CraftCMS\Taxonomies\Tags;
use App\Service\CraftCMS;
use DateTimeImmutable;
use Exception;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $search = $request->query->get('search');
        // Build queries
        $siteId = CraftCMS::getSiteForLocale($request->getLocale());
        $manager->add(
            'blogListing',
            new BlogListing(
                $siteId,
                null,
                null,
                null,
                null,
                $search,
                self::LIMIT,
                $currentPage
            )
        );

        return $this->buildListing($manager, $siteId, $currentPage, $search);
    }

    /**
     * @Route("/blog/{year}", requirements={"year": "\d\d\d\d"})
     *
     * @param QueryManager $manager
     * @param int          $year
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function archive(QueryManager $manager, int $year, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');
        // Build queries
        $siteId = CraftCMS::getSiteForLocale($request->getLocale());
        $manager->add(
            'blogListing',
            new BlogListing(
                $siteId,
                null,
                null,
                $year + 1,
                $year,
                $search,
                self::LIMIT,
                $currentPage
            )
        );

        return $this->buildListing($manager, $siteId, $currentPage, $search);
    }

    /**
     * @Route("/blog/categories/{category}", requirements={"category": ".+"})
     *
     * @param QueryManager $manager
     * @param string       $category
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function category(QueryManager $manager, string $category, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');
        // Build queries
        $siteId = CraftCMS::getSiteForLocale($request->getLocale());

        $manager->add('categories', new Categories($siteId, 'blogCategories'));
        $categories = $manager->getCollection('categories');
        $categoryId = null;
        foreach ($categories as $categoryData) {
            if ($categoryData['slug'] == $category) {
                $categoryId = $categoryData['id'];
                break;
            }
        }

        if ($categoryId == null) {
            throw $this->createNotFoundException('Category not found');
        }

        $manager->add(
            'blogListing',
            new BlogListing(
                $siteId,
                $categoryId,
                null,
                null,
                null,
                $search,
                self::LIMIT,
                $currentPage
            )
        );

        return $this->buildListing($manager, $siteId, $currentPage, $search);
    }

    /**
     * @Route("/blog/tags/{tag}", requirements={"tag": ".+"})
     *
     * @param QueryManager $manager
     * @param string       $tag
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function tag(QueryManager $manager, string $tag, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        $search      = $request->query->get('search');
        // Build queries
        $siteId = CraftCMS::getSiteForLocale($request->getLocale());

        $manager->add('tags', new Tags($siteId, 'blogTags'));
        $tags = $manager->getCollection('tags');
        $tagId = null;
        foreach ($tags as $tagData) {
            if ($tagData['slug'] == $tag) {
                $tagId = $tagData['id'];
                break;
            }
        }

        if ($tagId == null) {
            throw $this->createNotFoundException('Tag not found');
        }

        $manager->add(
            'blogListing',
            new BlogListing(
                $siteId,
                null,
                $tagId,
                null,
                null,
                $search,
                self::LIMIT,
                $currentPage
            )
        );

        return $this->buildListing($manager, $siteId, $currentPage, $search);
    }

    /**
     * @param QueryManager $manager
     * @param int          $siteId
     * @param int          $currentPage
     * @param string|null  $search
     *
     * @return RedirectResponse|Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws Exception
     */
    protected function buildListing(QueryManager $manager, int $siteId, int $currentPage, ?string $search)
    {
        $manager->add('filters', new BlogFilters($siteId));

        $collection = $manager->getCollection('blogListing');
        $pagination = $collection->getPagination();

        if (empty($collection) && $currentPage !== 1) {
            return $this->redirectToRoute('app_blog_index', ['page' => 1]);
        }
        if ($currentPage > $pagination->getTotalPages() && $pagination->getTotalPages() > 0) {
            return $this->redirectToRoute('app_blog_index', ['page' => 1]);
        }

        $page       = $manager->get('blogListing', '[entry]');
        $categories = $manager->getCollection('filters', '[categories]');
        $first      = $manager->get('filters', '[first]');
        $last       = $manager->get('filters', '[last]');

        $archives = range(
            (new DateTimeImmutable($first['postDate']))->format('Y'),
            (new DateTimeImmutable($last['postDate']))->format('Y')
        );

        dump($archives);
        dump($page);
        dump($collection);
        dump($pagination);
        dump($categories);

        return $this->render('blog/index.html.twig', [
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $pagination,
            'categories' => $categories,
            'archives'   => $archives,
            'search'     => $search
        ]);
    }
}
