<?php

namespace App\Controller;

use App\Form\CommentType;
use App\Query\CraftCMS\Blog\Collection as BlogCollection;
use App\Query\CraftCMS\Blog\CreateComment;
use App\Query\CraftCMS\Blog\Entry;
use App\Query\CraftCMS\Blog\Filters;
use App\Query\CraftCMS\Blog\Comments;
use App\Query\CraftCMS\Blog\Listing;
use App\Query\CraftCMS\Taxonomies\Categories;
use App\Query\CraftCMS\Taxonomies\Tags;
use App\Query\CraftCMS\YouMayAlsoLikeRelatedEntries;
use DateTimeImmutable;
use Exception;
use Strata\Data\Collection;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Jean-Guilhem Rouel <jean-gui@w3.org>
 *
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    private const LIMIT = 10;

    /**
     * @Route("/")
     *
     * @param QueryManager        $manager
     * @param Site                $site
     * @param Request             $request
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function index(
        QueryManager $manager,
        Site $site,
        Request $request,
        RouterInterface $router,
        TranslatorInterface $translator
    ): Response {
        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');
        
        $manager->add('page', new Listing($site->siteId));
        $manager->add(
            'collection',
            new BlogCollection(
                $router,
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

        [$page, $collection, $categories, $archives] = $this->buildListing(
            $manager,
            $site,
            $currentPage,
            $router,
            $translator
        );

        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');
        $page['breadcrumbs'] = [
            'title'  => $singlesBreadcrumbs['blog']['title'],
            'url'    => $singlesBreadcrumbs['blog']['url'],
            'parent' => $singlesBreadcrumbs['homepage']
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
     * @Route("/{year}/", requirements={"year": "\d\d\d\d"})
     *
     * @param QueryManager        $manager
     * @param int                 $year
     * @param Site                $site
     * @param Request             $request
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function archive(
        QueryManager $manager,
        int $year,
        Site $site,
        Request $request,
        RouterInterface $router,
        TranslatorInterface $translator
    ): Response {
        $currentPage = $request->query->get('page', 1);
        $search = $request->query->get('search');

        $manager->add('page', new Listing($site->siteId));
        $manager->add(
            'collection',
            new BlogCollection(
                $router,
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

        [$page, $collection, $categories, $archives] = $this->buildListing(
            $manager,
            $site,
            $currentPage,
            $router,
            $translator
        );

        $singlesBreadcrumbs  = $manager->get('singles-breadcrumbs');
        $page['breadcrumbs'] = [
            'title' => $year,
            'url' => $this->generateUrl('app_blog_archive', ['year' => $year]),
            'parent' => [
                'title'  => $singlesBreadcrumbs['blog']['title'],
                'url'    => $singlesBreadcrumbs['blog']['url'],
                'parent' => $singlesBreadcrumbs['homepage']
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
     * @Route("/category/{slug}/", requirements={"slug": "[^/]+"})
     *
     * @param QueryManager        $manager
     * @param string              $slug
     * @param Site                $site
     * @param Request             $request
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function category(
        QueryManager $manager,
        string $slug,
        Site $site,
        Request $request,
        RouterInterface $router,
        TranslatorInterface $translator
    ): Response {
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
            throw $this->createNotFoundException('CategoryInfo not found');
        }

        $manager->add('page', new Listing($site->siteId));
        $manager->add(
            'collection',
            new BlogCollection(
                $router,
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

        [$page, $collection, $categories, $archives] = $this->buildListing(
            $manager,
            $site,
            $currentPage,
            $router,
            $translator
        );
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['breadcrumbs'] = [
            'title'  => $category['title'],
            'url'    => $this->generateUrl('app_blog_category', ['slug' => $slug]),
            'parent' => [
                'title'  => $singlesBreadcrumbs['blog']['title'],
                'url'    => $singlesBreadcrumbs['blog']['url'],
                'parent' => $singlesBreadcrumbs['homepage']
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
     * @Route("/tags/{slug}/", requirements={"tag": "[^/]+"})
     *
     * @param QueryManager        $manager
     * @param string              $slug
     * @param Site                $site
     * @param Request             $request
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    public function tag(
        QueryManager $manager,
        string $slug,
        Site $site,
        Request $request,
        RouterInterface $router,
        TranslatorInterface $translator
    ): Response {
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

        $manager->add('page', new Listing($site->siteId));
        $manager->add(
            'collection',
            new BlogCollection(
                $router,
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

        [$page, $collection, $categories, $archives] = $this->buildListing(
            $manager,
            $site,
            $currentPage,
            $router,
            $translator
        );
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');

        $page['breadcrumbs'] = [
            'title'  => $tag['title'],
            'url'    => $this->generateUrl('app_blog_tag', ['slug' => $slug]),
            'parent' => [
                'title'  => $singlesBreadcrumbs['blog']['title'],
                'url'    => $singlesBreadcrumbs['blog']['url'],
                'parent' => $singlesBreadcrumbs['homepage']
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
     * @Route("/{year}/{slug}/", requirements={"year": "\d\d\d\d"})
     *
     * @param int                 $year
     * @param string              $slug
     * @param QueryManager        $manager
     * @param RouterInterface     $router
     * @param Site                $site
     * @param Request             $request
     * @param TranslatorInterface $translator
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     * @throws Exception
     */
    public function show(
        int $year,
        string $slug,
        QueryManager $manager,
        RouterInterface $router,
        Site $site,
        Request $request,
        TranslatorInterface $translator
    ): Response {
        $manager->add('page', new Entry($site->siteId, $year, $slug, $router));

        $page = $manager->get('page');
        if (empty($page)) {
            throw $this->createNotFoundException('Page not found');
        }

        $replyTo    = $request->query->get('replytocom');
        $newComment = ['post' => $page['id'], 'parent' => $replyTo];
        $form = $this->createForm(CommentType::class, $newComment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment = $form->getData();

            $manager->add(
                'create-comment',
                (new CreateComment(
                    $newComment['post'],
                    $newComment['name'],
                    $newComment['email'],
                    $newComment['comment'],
                    $newComment['parent']
                ))->setOptions(['auth_bearer' => $this->getParameter('app.craftcms_api_publish_token')])
            );

            $response = $manager->get('create-comment');

            if ($this->getParameter('kernel.environment') == 'dev') {
                dump($response);
            }

            $this->addFlash(
                'success',
                $translator->trans('blog.comments.form.success', [])
            );
            $this->addFlash(
                'title-success',
                $translator->trans('notes.successes.default_title', [], 'w3c_website_templates_bundle')
            );

            return $this->redirectToRoute('app_blog_show', ['year' => $year, 'slug' => $slug]);
        }

        $postYear = intval((new DateTimeImmutable($page['postDate']))->format('Y'));
        if ($year !== $postYear) {
            return $this->redirectToRoute('app_blog_show', ['slug' => $slug, 'year' => $postYear]);
        }

        $manager->add(
            'crosslinks',
            new YouMayAlsoLikeRelatedEntries($router, $site->siteId, $page['id'])
        );

        $manager->add('comments', new Comments($page['id']));

        $crosslinks = $manager->get('crosslinks');
        $singlesBreadcrumbs = $manager->get('singles-breadcrumbs');
        $comments = $manager->getCollection('comments');

        $topLevelComms = $this->buildComments($comments);

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($topLevelComms);
        }

        $page['seo']['expiry'] = $page['expiryDate'];
        $page['breadcrumbs'] = [
            'title'  => $page['title'],
            'url'    => $this->generateUrl('app_blog_show', ['year' => $year, 'slug' => $slug]),
            'parent' => [
                'title'  => $year,
                'url'    => $this->generateUrl('app_blog_archive', ['year' => $year]),
                'parent' => [
                    'title'  => $singlesBreadcrumbs['blog']['title'],
                    'url'    => $singlesBreadcrumbs['blog']['url'],
                    'parent' => $singlesBreadcrumbs['homepage']
                ]
            ]
        ];

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
            dump($crosslinks);
            dump($singlesBreadcrumbs);
            dump($comments);
        }

        return $this->render('blog/show.html.twig', [
            'site'          => $site,
            'navigation'    => $manager->getCollection('navigation'),
            'page'          => $page,
            'crosslinks'    => $crosslinks,
            'comments'      => $topLevelComms,
            'commentsCount' => count($comments),
            'year'          => $year,
            'slug'          => $slug,
            'comment_form'  => $form->createView(),
            'form_errors'   => $form->getErrors(true),
            'reply_to'      => $replyTo
        ]);
    }

    /**
     * @param QueryManager        $manager
     * @param Site                $site
     * @param int                 $currentPage
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     *
     * @return RedirectResponse|array
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    protected function buildListing(
        QueryManager $manager,
        Site $site,
        int $currentPage,
        RouterInterface $router,
        TranslatorInterface $translator
    ): array {
        $manager->add('filters', new Filters($router, $translator, $site->siteId));

        $collection = $manager->getCollection('collection');

        $pagination = $collection->getPagination();

        if (empty($collection) && $currentPage !== 1) {
            return $this->redirectToRoute('app_blog_index', ['page' => 1]);
        }

        if ($currentPage > $pagination->getTotalPages() && $pagination->getTotalPages() > 0) {
            return $this->redirectToRoute('app_blog_index', ['page' => 1]);
        }

        $page                  = $manager->get('page');
        $filters               = $manager->get('filters');
        $categories            = $filters['categories'];
        $archives              = $filters['archives'];
        $page['seo']['expiry'] = $page['expiryDate'];

        if ($this->getParameter('kernel.environment') == 'dev') {
            dump($page);
            dump($collection);
            dump($pagination);
            dump($filters);
        }

        return [$page, $collection, $categories, $archives];
    }

    private function getRootComment($reply, $replies, $roots)
    {
        $parentId = $reply['parent']['id'];
        if (array_key_exists($parentId, $roots)) {
            return $roots[$parentId]['id'];
        }

        return $this->getRootComment($replies[$parentId], $replies, $roots);
    }

    /**
     * @param Collection $comments
     *
     * @return array
     */
    private function buildComments(Collection $comments): array
    {
        $replies       = [];
        $topLevelComms = [];
        foreach ($comments as $comment) {
            if ($comment['level'] === 1) {
                $comment['children']           = [];
                $topLevelComms[$comment['id']] = $comment;
            } else {
                $replies[$comment['id']] = $comment;
            }
        }

        foreach ($replies as $reply) {
            $rootId = $this->getRootComment($reply, $replies, $topLevelComms);
            $topLevelComms[$rootId]['children'][$reply['id']] = $reply;
        }

        return $topLevelComms;
    }
}
