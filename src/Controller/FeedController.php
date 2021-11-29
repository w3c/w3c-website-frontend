<?php

namespace App\Controller;

use App\Query\CraftCMS\Feeds\Blog;
use App\Query\CraftCMS\Feeds\Comments;
use App\Query\CraftCMS\Feeds\Events;
use App\Query\CraftCMS\Feeds\News;
use App\Query\CraftCMS\Feeds\PressReleases;
use App\Query\CraftCMS\Feeds\Taxonomy;
use App\Query\CraftCMS\Taxonomies\CategoryInfo;
use App\Query\CraftCMS\Taxonomies\GroupInfo;
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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class FeedController extends AbstractController
{
    private const LIMIT = 25;

    /**
     * @Route("/blog/feed/")
     *
     * @param QueryManager $manager
     * @param Site         $site
     * @param Environment  $twig
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws QueryManagerException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function blog(QueryManager $manager, Site $site, Environment $twig): Response
    {
        $manager->add('rss', new Blog($site->siteId, self::LIMIT));
        $feedUrl = $this->generateUrl('app_feed_blog', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_blog_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $entries       = $manager->getCollection('rss');
        $commentCounts = $this->getBlogCommentCounts($entries, $manager);

        $feed = new Feed();
        $feed->setTitle("W3C");
        $feed->setLanguage($site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription('Description');

        foreach ($entries as $data) {
            if (array_key_exists($data['id'], $commentCounts)) {
                $data['comments'] = $commentCounts[$data['id']];
            } else {
                $data['comments'] = 0;
            }
            $feed->addEntry($this->buildBlogEntry($data, $feed, $twig));
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @Route("/news/feed/")
     *
     * @param QueryManager $manager
     * @param Site         $site
     * @param Environment  $twig
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function news(QueryManager $manager, Site $site, Environment $twig): Response
    {
        $manager->add('rss', new News($site->siteId, self::LIMIT));
        $entries = $manager->getCollection('rss');

        $feedUrl = $this->generateUrl('app_feed_news', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_news_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $feed = new Feed();
        $feed->setTitle("W3C - News");
        $feed->setLanguage($site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription('Description');

        foreach ($entries as $data) {
            $feed->addEntry($this->buildNewsEntry($data, $feed, $twig));
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @Route("/press-releases/feed/")
     *
     * @param QueryManager $manager
     * @param Site         $site
     * @param Environment  $twig
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function pressReleases(QueryManager $manager, Site $site, Environment $twig): Response
    {
        $manager->add('rss', new PressReleases($site->siteId, self::LIMIT));
        $entries = $manager->getCollection('rss');

        $feedUrl = $this->generateUrl('app_feed_pressreleases', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_pressreleases_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $feed = new Feed();
        $feed->setTitle("W3C - Press Releases");
        $feed->setLanguage($site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription('Description');

        foreach ($entries as $data) {
            $feed->addEntry($this->buildPressReleaseEntry($data, $feed, $twig));
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @Route("/feeds/category/{slug}/")
     *
     * @param string       $slug
     * @param QueryManager $manager
     * @param Site         $site
     * @param Environment  $twig
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function category(string $slug, QueryManager $manager, Site $site, Environment $twig): Response
    {
        $manager->add('category-info', new CategoryInfo($site->siteId, 'blogCategories', $slug));
        $category = $manager->get('category-info');

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $manager->add('rss', new Taxonomy($site->siteId, self::LIMIT, $category['id']));

        $entries = $manager->getCollection('rss');
        $feedUrl = $this->generateUrl(
            'app_feed_category',
            ['slug' => $site->siteId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->buildTaxonomyFeed($entries, $manager, $category['title'], $site, $feedUrl, $twig);
    }

    /**
     * @Route("/feeds/ecosystem/{slug}/")
     *
     * @param string       $slug
     * @param QueryManager $manager
     * @param Site         $site
     * @param Environment  $twig
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function ecosystem(string $slug, QueryManager $manager, Site $site, Environment $twig): Response
    {
        $manager->add('ecosystem-info', new CategoryInfo($site->siteId, 'ecosystems', $slug));
        $ecosystem = $manager->get('ecosystem-info');

        if (!$ecosystem) {
            throw $this->createNotFoundException('Category not found');
        }

        $manager->add('rss', new Taxonomy($site->siteId, self::LIMIT, null, $ecosystem['id']));

        $entries = $manager->getCollection('rss');
        $feedUrl = $this->generateUrl(
            'app_feed_ecosystem',
            ['slug' => $site->siteId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->buildTaxonomyFeed($entries, $manager, $ecosystem['title'], $site, $feedUrl, $twig);
    }

    /**
     * @Route("/feeds/groups/{type}/{slug}/")
     *
     * @param QueryManager $manager
     * @param Site         $site
     * @param Environment  $twig
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function group(string $type, string $slug, QueryManager $manager, Site $site, Environment $twig): Response
    {
        $manager->add('group-info', new GroupInfo($site->siteId, $type, $slug));
        $group = $manager->get('group-info');

        if (!$group) {
            throw $this->createNotFoundException('Category not found');
        }

        $manager->add('rss', new Taxonomy($site->siteId, self::LIMIT, null, null, $group['id']));

        $entries = $manager->getCollection('rss');
        $feedUrl = $this->generateUrl(
            'app_feed_ecosystem',
            ['slug' => $site->siteId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->buildTaxonomyFeed($entries, $manager, $group['title'], $site, $feedUrl, $twig);
    }

    /**
     * @Route("/events/feed/")
     *
     * @param string       $type
     * @param string       $slug
     * @param QueryManager $manager
     * @param Site         $site
     * @param Environment  $twig
     *
     * @return Response
     * @throws GraphQLQueryException
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws QueryManagerException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function events(QueryManager $manager, Site $site, Environment $twig): Response
    {
        $manager->add('rss', new Events($site->siteId, self::LIMIT));
        $entries = $manager->getCollection('rss');

        $feedUrl = $this->generateUrl('app_feed_events', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_events_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $feed = new Feed();
        $feed->setTitle("W3C - Events");
        $feed->setLanguage($site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription('Description');

        foreach ($entries as $data) {
            $feed->addEntry($this->buildEventEntry($data, $feed, $twig));
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @param Collection   $entries
     * @param QueryManager $manager
     *
     * @return array
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
     * @param array       $data
     * @param Feed        $feed
     * @param Environment $twig
     *
     * @return Entry
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function buildBlogEntry(array $data, Feed $feed, Environment $twig): Entry
    {
        $date  = new DateTimeImmutable($data['date']);

        $entry = $this->buildBasicEntry($data, $feed, $date, $twig);

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
     * @param array       $data
     * @param Feed        $feed
     * @param Environment $twig
     *
     * @return Entry
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function buildEventEntry(array $data, Feed $feed, Environment $twig): Entry
    {
        $date = new DateTimeImmutable($data['date']);

        $entry = $this->buildBasicEntry($data, $feed, $date, $twig);

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
     * @param array       $data
     * @param Feed        $feed
     * @param Environment $twig
     *
     * @return Entry
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function buildNewsEntry(array $data, Feed $feed, Environment $twig): Entry
    {
        $date = new DateTimeImmutable($data['date']);

        $entry = $this->buildBasicEntry($data, $feed, $date, $twig);

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
     * @param array       $data
     * @param Feed        $feed
     * @param Environment $twig
     *
     * @return Entry
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function buildPressReleaseEntry(array $data, Feed $feed, Environment $twig): Entry
    {
        $date = new DateTimeImmutable($data['date']);

        $entry = $this->buildBasicEntry($data, $feed, $date, $twig);

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
     * @param Feed              $feed
     * @param array             $data
     * @param DateTimeImmutable $date
     * @param Environment       $twig
     *
     * @return Entry
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function buildBasicEntry(array $data, Feed $feed, DateTimeImmutable $date, Environment $twig): Entry
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
                $twig->render('rss_entry.html.twig', ['components' => $data['defaultFlexibleComponents']])
            );
        } elseif (array_key_exists('pageContent', $data) && $data['pageContent']) {
            $entry->setContent($data['pageContent']);
        }

        return $entry;
    }

    /**
     * @param Collection $entries
     * @param QueryManager $manager
     * @param              $title
     * @param Site $site
     * @param string $feedUrl
     * @param Environment $twig
     *
     * @return Response
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
        Site $site,
        string $feedUrl,
        Environment $twig
    ): Response {
        $commentCounts = $this->getBlogCommentCounts($entries, $manager);

        $feed = new Feed();
        $feed->setTitle("W3C - " . $title);
        $feed->setDescription('Description');
        $feed->setLanguage($site->getLocale());

        $feed->setLink('https://www.w3.org/');
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
                    $feed->addEntry($this->buildBlogEntry($data, $feed, $twig));
                    break;
                case 'events':
                    $feed->addEntry($this->buildEventEntry($data, $feed, $twig));
                    break;
                case 'newsArticles':
                    $feed->addEntry($this->buildNewsEntry($data, $feed, $twig));
                    break;
                case 'pressReleases':
                    $feed->addEntry($this->buildPressReleaseEntry($data, $feed, $twig));
                    break;
            }
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }
}
