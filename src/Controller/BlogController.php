<?php

namespace App\Controller;

use App\Query\CraftCMS\Blog\Entry;
use App\Query\CraftCMS\Blog\Filters;
use App\Query\CraftCMS\Blog\Listing;
use App\Query\CraftCMS\Taxonomies\Categories;
use App\Query\CraftCMS\Taxonomies\Tags;
use App\Service\CraftCMS;
use DateTimeImmutable;
use Exception;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private const LIMIT = 10;

    /**
     * @Route("/blog/")
     *
     * @param QueryManager $manager
     * @param Site         $site
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function index(QueryManager $manager, Site $site, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');
        
        $manager->add(
            'blogListing',
            new Listing(
                $site->siteId,
                null,
                null,
                null,
                null,
                $search,
                self::LIMIT,
                $currentPage
            )
        );

        [$page, $collection, $categories, $archives] = $this->buildListing($manager, $site, $currentPage);
        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'uri'    => $page['uri'],
            'parent' => null
        ];

        return $this->render('blog/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'categories' => $categories,
            'archives'   => $archives,
            'search'     => $search
        ]);
    }

    /**
     * @Route("/blog/{year}", requirements={"year": "\d\d\d\d"})
     *
     * @param QueryManager $manager
     * @param int          $year
     * @param Site         $site
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function archive(QueryManager $manager, int $year, Site $site, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');

        $manager->add(
            'blogListing',
            new Listing(
                $site->siteId,
                null,
                null,
                $year + 1,
                $year,
                $search,
                self::LIMIT,
                $currentPage
            )
        );

        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        [$page, $collection, $categories, $archives] = $this->buildListing($manager, $site, $currentPage);
        $page['breadcrumbs'] = [
            'title' => $year,
            'uri' => $singlesBreadcrumbs['blog']['uri'] . '/' . $year,
            'parent' => [
                'title'  => $singlesBreadcrumbs['blog']['title'],
                'uri'    => $singlesBreadcrumbs['blog']['uri'],
                'parent' => null
            ]
        ];
        $page['title'] = $page['title'] . ' - ' . $year;

        return $this->render('blog/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'categories' => $categories,
            'archives'   => $archives,
            'search'     => $search
        ]);
    }

    /**
     * @Route("/blog/category/{slug}", requirements={"category": ".+"})
     *
     * @param QueryManager $manager
     * @param string       $slug
     * @param Site         $site
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function category(QueryManager $manager, string $slug, Site $site, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');

        $manager->add('categories', new Categories($site->siteId, 'blogCategories'));
        $categories = $manager->getCollection('categories');

        $category = [];
        foreach ($categories as $categoryData) {
            if ($categoryData['slug'] == $slug) {
                $category = $categoryData;
                break;
            }
        }

        if ($category['id'] == null) {
            throw $this->createNotFoundException('Category not found');
        }

        $manager->add(
            'blogListing',
            new Listing(
                $site->siteId,
                $category['id'],
                null,
                null,
                null,
                $search,
                self::LIMIT,
                $currentPage
            )
        );

        [$page, $collection, $categories, $archives] = $this->buildListing($manager, $site, $currentPage);
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['breadcrumbs'] = [
            'title'  => $category['title'],
            'uri'    => $singlesBreadcrumbs['blog']['uri'] . '/category/' . $slug,
            'parent' => [
                'title'  => $singlesBreadcrumbs['blog']['title'],
                'uri'    => $singlesBreadcrumbs['blog']['uri'],
                'parent' => null
            ]
        ];
        $page['title']       = $page['title'] . ' - ' . $category['title'];

        return $this->render('blog/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'categories' => $categories,
            'archives'   => $archives,
            'search'     => $search
        ]);
    }

    /**
     * @Route("/blog/tag/{slug}", requirements={"tag": ".+"})
     *
     * @param QueryManager $manager
     * @param string       $slug
     * @param Site         $site
     * @param Request      $request
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function tag(QueryManager $manager, string $slug, Site $site, Request $request): Response
    {
        $currentPage = $request->query->get('page', 1);
        $search      = $request->query->get('search');

        $manager->add('tags', new Tags($site->siteId, 'blogTags'));
        $tags = $manager->getCollection('tags');
        $tag = [];
        foreach ($tags as $tagData) {
            if ($tagData['slug'] == $slug) {
                $tag = $tagData;
                break;
            }
        }

        if ($tag['id'] == null) {
            throw $this->createNotFoundException('Tag not found');
        }

        $manager->add(
            'blogListing',
            new Listing(
                $site->siteId,
                null,
                $tag['id'],
                null,
                null,
                $search,
                self::LIMIT,
                $currentPage
            )
        );

        [$page, $collection, $categories, $archives] = $this->buildListing($manager, $site, $currentPage);
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['breadcrumbs'] = [
            'title'  => $tag['title'],
            'uri'    => $singlesBreadcrumbs['blog']['uri'] . '/tag/' . $slug,
            'parent' => [
                'title'  => $singlesBreadcrumbs['blog']['title'],
                'uri'    => $singlesBreadcrumbs['blog']['uri'],
                'parent' => null
            ]
        ];
        $page['title']       = $page['title'] . ' - ' . $tag['title'];

        return $this->render('blog/index.html.twig', [
            'site'       => $site,
            'navigation' => $manager->getCollection('navigation'),
            'page'       => $page,
            'entries'    => $collection,
            'pagination' => $collection->getPagination(),
            'categories' => $categories,
            'archives'   => $archives,
            'search'     => $search
        ]);
    }

    /**
     * @param QueryManager $manager
     * @param Site         $site
     * @param int          $currentPage
     *
     * @return RedirectResponse|array
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws Exception
     */
    protected function buildListing(QueryManager $manager, Site $site, int $currentPage): array
    {
        $manager->add('filters', new Filters($site->siteId));

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

        $page['seo']['expiry'] = $page['expiryDate'];

        dump($archives);
        dump($page);
        dump($collection);
        dump($pagination);
        dump($categories);

        return [$page, $collection, $categories, $archives];
    }
}
