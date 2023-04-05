<?php

namespace App\Controller;

use App\Query\CraftCMS\Blog\Listing as BlogListing;
use App\Query\CraftCMS\Ecosystems\Ecosystem as CraftEcosystem;
use App\Query\CraftCMS\Events\Page;
use App\Query\CraftCMS\Feeds\Blog;
use App\Query\CraftCMS\Feeds\Comments;
use App\Query\CraftCMS\Feeds\Events;
use App\Query\CraftCMS\Feeds\News;
use App\Query\CraftCMS\Feeds\PressReleases;
use App\Query\CraftCMS\Feeds\Taxonomy;
use App\Query\CraftCMS\News\Listing as NewsListing;
use App\Query\CraftCMS\PressReleases\Listing as PressReleasesListing;
use App\Query\CraftCMS\Taxonomies\CategoryInfo;
use App\Query\CraftCMS\Taxonomies\GroupInfo;
use App\Query\W3C\Group;
use DateTimeImmutable;
use Exception;
use Laminas\Feed\Writer\Entry;
use Laminas\Feed\Writer\Feed;
use Strata\Data\Collection;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryManagerException;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Exception\InvalidLocaleException;
use Strata\Frontend\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class FeedController extends AbstractController
{
    private const LIMIT = 25;

    private Site $site;
    private Environment $twig;
    private RouterInterface $router;

    public function __construct(Site $site, Environment $twig, RouterInterface $router)
    {
        $this->site   = $site;
        $this->twig   = $twig;
        $this->router = $router;
    }

    /**
     * @Route("/blog/feed/")
     *
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws QueryManagerException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function blog(QueryManager $manager): Response
    {
        $manager->add('rss', new Blog($this->site->siteHandle, self::LIMIT));
        $manager->add('page', new BlogListing($this->site->siteHandle));
        $feedUrl = $this->generateUrl('app_feed_blog', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_blog_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $entries       = $manager->getCollection('rss');
        $commentCounts = $this->getBlogCommentCounts($entries, $manager);
        $page          = $manager->get('page');

        $feed = new Feed();
        $feed->setTitle("W3C - " . $page['title']);
        $feed->setLanguage($this->site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription($page['excerpt'] ?? $page['title']);

        foreach ($entries as $data) {
            if (array_key_exists($data['id'], $commentCounts)) {
                $data['comments'] = $commentCounts[$data['id']];
            } else {
                $data['comments'] = 0;
            }
            $feed->addEntry($this->buildBlogEntry($data, $feed));
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @Route("/news/feed/")
     *
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function news(QueryManager $manager): Response
    {
        $manager->add('rss', new News($this->site->siteHandle, self::LIMIT));
        $manager->add('page', new NewsListing($this->site->siteHandle));
        $entries = $manager->getCollection('rss');
        $page    = $manager->get('page');

        $feedUrl = $this->generateUrl('app_feed_news', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_news_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $feed = new Feed();
        $feed->setTitle("W3C - " . $page['title']);
        $feed->setLanguage($this->site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription($page['excerpt'] ?? $page['title']);

        foreach ($entries as $data) {
            $feed->addEntry($this->buildNewsEntry($data, $feed));
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @Route("/press-releases/feed/")
     *
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function pressReleases(QueryManager $manager): Response
    {
        $manager->add('rss', new PressReleases($this->site->siteHandle, self::LIMIT));
        $manager->add('page', new PressReleasesListing($this->site->siteHandle));

        $entries = $manager->getCollection('rss');
        $page    = $manager->get('page');

        $feedUrl = $this->generateUrl('app_feed_pressreleases', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_pressreleases_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $feed = new Feed();
        $feed->setTitle('W3C - ' . $page['title']);
        $feed->setLanguage($this->site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription($page['excerpt'] ?? $page['title']);

        foreach ($entries as $data) {
            $feed->addEntry($this->buildPressReleaseEntry($data, $feed));
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @Route("/categories/{slug}/feed/")
     *
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function category(string $slug, QueryManager $manager): Response
    {
        $manager->add('category-info', new CategoryInfo($this->site->siteHandle, 'blogCategories', $slug));
        $category = $manager->get('category-info');

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $manager->add('rss', new Taxonomy($this->site->siteHandle, self::LIMIT, $category['id']));

        $entries = $manager->getCollection('rss');
        $feedUrl = $this->generateUrl('app_feed_category', ['slug' => $slug], UrlGeneratorInterface::ABSOLUTE_URL);

        // Category feeds don't have a meaningful description, so we use the title. They also don't have a
        // dedicated page as they contain posts, news and other content types, so we link to the default URL.
        return $this->buildTaxonomyFeed(
            $entries,
            $manager,
            $category['title'],
            $feedUrl,
            $category['title']
        );
    }

    /**
     * @Route("/ecosystems/{slug}/feed/")
     *
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws NotFoundHttpException
     */
    public function ecosystem(string $slug, QueryManager $manager): Response
    {
        $manager->add('ecosystem-info', new CategoryInfo($this->site->siteHandle, 'ecosystems', $slug));
        $ecosystem = $manager->get('ecosystem-info');

        if (!$ecosystem) {
            throw $this->createNotFoundException('Category not found');
        }

        $manager->add('rss', new Taxonomy($this->site->siteHandle, self::LIMIT, null, $ecosystem['id']));
        $manager->add('page', new CraftEcosystem($this->router, $this->site->siteHandle, $slug));
        $page = $manager->get('page');
        $title = $ecosystem['title'] . ' Ecosystem';

        if (array_key_exists('pageLead', $page) && $page['pageLead']) {
            $description = $page['pageLead'];
        } elseif (array_key_exists('excerpt', $page) && $page['excerpt']) {
            $description = $page['excerpt'];
        } else {
            $description = $title;
        }

        $entries = $manager->getCollection('rss');
        $feedUrl = $this->generateUrl('app_feed_ecosystem', ['slug' => $slug], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_ecosystem_show', ['slug' => $slug], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->buildTaxonomyFeed(
            $entries,
            $manager,
            $title,
            $feedUrl,
            $description,
            $pageUrl,
        );
    }

    /**
     * @Route("/groups/{type}/{shortname}/feed/")
     *
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function group(string $type, string $shortname, QueryManager $manager): Response
    {
        $slug = $type . '-' . $shortname;
        $manager->add('group-info', new GroupInfo($this->site->siteHandle, $slug));
        $manager->add('group', new Group($type, $shortname));
        $cmsGroup = $manager->get('group-info');
        $group    = $manager->get('group');

        if (!$cmsGroup || !$group) {
            throw $this->createNotFoundException('Group not found');
        }

        $manager->add('rss', new Taxonomy($this->site->siteHandle, self::LIMIT, null, null, $cmsGroup['id']));

        $entries = $manager->getCollection('rss');
        $feedUrl = $this->generateUrl(
            'app_feed_group',
            ['type' => $type, 'shortname' => $shortname],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $pageUrl = $group['_links']['homepage']['href'];

        return $this->buildTaxonomyFeed(
            $entries,
            $manager,
            $group['name'],
            $feedUrl,
            $group['description'],
            $pageUrl
        );
    }

    /**
     * @Route("/events/feed/")
     *
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function events(QueryManager $manager): Response
    {
        $manager->add('rss', new Events($this->site->siteHandle, self::LIMIT));
        $manager->add('page', new Page($this->site->siteHandle));
        $entries = $manager->getCollection('rss');
        $page    = $manager->get('page');

        $feedUrl = $this->generateUrl('app_feed_events', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_events_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $feed = new Feed();
        $feed->setTitle('W3C - ' . $page['title']);
        $feed->setLanguage($this->site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription($page['excerpt'] ?? $page['title']);

        foreach ($entries as $data) {
            $feed->addEntry($this->buildEventEntry($data, $feed));
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @throws GraphQLQueryException
     * @throws QueryManagerException
     */
    private function getBlogCommentCounts(Collection $entries, QueryManager $manager): array
    {
        $postIds = array_map(function (array $entry) {
            return $entry['id'];
        }, $entries->getCollection());
        $manager->add('comments', new Comments($postIds));

        $commentCounts = [];
        foreach ($manager->getCollection('comments') as $comment) {
            if (array_key_exists($comment['ownerId'], $commentCounts)) {
                $commentCounts[$comment['ownerId']]++;
            } else {
                $commentCounts[$comment['ownerId']] = 1;
            }
        }

        return $commentCounts;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function buildBlogEntry(array $data, Feed $feed): Entry
    {
        $date  = new DateTimeImmutable($data['date']);

        $entry = $this->buildBasicEntry($data, $feed, $date);

        $url = $this->generateUrl(
            'app_blog_show',
            ['year' => $data['year'], 'slug' => $data['slug']],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $entry->setLink($url);
        $entry->setCommentLink($url . '#comments');
        $entry->setCommentCount($data['comments']);
        $entry->addCategory(['term' => 'blogs', 'label' => 'Blog']);

        return $entry;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function buildEventEntry(array $data, Feed $feed): Entry
    {
        $date = new DateTimeImmutable($data['date']);

        $entry = $this->buildBasicEntry($data, $feed, $date);

        switch ($data['typeHandle']) {
            case 'external':
                $entry->setLink($data['urlLink']);
                break;
            case 'entryContentIsACraftPage':
                $entry->setLink(
                    $this->generateUrl(
                        'app_default_index',
                        ['route' => $data['page'][0]['uri']],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                );
                break;
            default:
                $entry->setLink(
                    $this->generateUrl(
                        'app_events_show',
                        ['type' => $data['type'][0]['slug'], 'year' => $data['year'], 'slug' => $data['slug']],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                );
        }

        $entry->addCategory(['term' => $data['type'][0]['slug'], 'label' => $data['type'][0]['title']]);

        return $entry;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function buildNewsEntry(array $data, Feed $feed): Entry
    {
        $date = new DateTimeImmutable($data['date']);

        $entry = $this->buildBasicEntry($data, $feed, $date);

        $entry->setLink(
            $this->generateUrl(
                'app_news_show',
                ['year' => $data['year'], 'slug' => $data['slug']],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
        $entry->addCategory(['term' => 'news', 'label' => 'News']);

        return $entry;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function buildPressReleaseEntry(array $data, Feed $feed): Entry
    {
        $date = new DateTimeImmutable($data['date']);

        $entry = $this->buildBasicEntry($data, $feed, $date);

        $entry->setLink(
            $this->generateUrl(
                'app_pressreleases_show',
                ['year' => $data['year'], 'slug' => $data['slug']],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
        $entry->addCategory(['term' => 'press-releases', 'label' => 'Press Release']);

        return $entry;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function buildBasicEntry(array $data, Feed $feed, DateTimeImmutable $date): Entry
    {
        $entry = $feed->createEntry();
        $entry->setTitle($data['title']);

        if (array_key_exists('authors', $data)) {
            $authors = implode(
                ', ',
                array_map(function ($author) {
                    return $author['name'];
                }, $data['authors'])
            );
            if ($authors) {
                $entry->addAuthor(['name' => $authors]);
            }
        }

        //$entry->setDateModified(time());
        $entry->setDateCreated($date);
        if (array_key_exists('excerpt', $data) && $data['excerpt']) {
            $entry->setDescription($data['excerpt']);
        }

        if (array_key_exists('categories', $data)) {
            foreach ($data['categories'] as $category) {
                $entry->addCategory(['term' => $category['slug'], 'label' => $category['title']]);
            }
        }

        if (array_key_exists('defaultFlexibleComponents', $data) &&
            count($data['defaultFlexibleComponents']) > 0
        ) {
            $entry->setContent(
                $this->twig->render('rss_entry.html.twig', ['components' => $data['defaultFlexibleComponents']])
            );
        } elseif (array_key_exists('pageContent', $data) && $data['pageContent']) {
            $entry->setContent($data['pageContent']);
        }

        return $entry;
    }

    /**
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function buildTaxonomyFeed(
        Collection $entries,
        QueryManager $manager,
        $title,
        string $feedUrl,
        string $description,
        string $pageUrl = null
    ): Response {
        $commentCounts = $this->getBlogCommentCounts($entries, $manager);

        $feed = new Feed();
        $feed->setTitle("W3C - " . $title);
        $feed->setDescription($description);
        $feed->setLanguage($this->site->getLocale());

        if ($pageUrl) {
            $feed->setLink($pageUrl);
        } else {
            $feed->setLink($this->router->generate('app_default_home', [], UrlGeneratorInterface::ABSOLUTE_URL));
        }
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());

        foreach ($entries as $data) {
            switch ($data['sectionHandle']) {
                case 'blogPosts':
                    if (array_key_exists($data['id'], $commentCounts)) {
                        $data['comments'] = $commentCounts[$data['id']];
                    } else {
                        $data['comments'] = 0;
                    }
                    $feed->addEntry($this->buildBlogEntry($data, $feed));
                    break;
                case 'events':
                    $feed->addEntry($this->buildEventEntry($data, $feed));
                    break;
                case 'newsArticles':
                    $feed->addEntry($this->buildNewsEntry($data, $feed));
                    break;
                case 'pressReleases':
                    $feed->addEntry($this->buildPressReleaseEntry($data, $feed));
                    break;
            }
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }
}
