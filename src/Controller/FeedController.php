<?php

namespace App\Controller;

use App\Query\CraftCMS\Feeds\Blog;
use App\Query\CraftCMS\Feeds\Comments;
use App\Query\CraftCMS\Feeds\News;
use App\Query\CraftCMS\Feeds\PressReleases;
use App\Query\CraftCMS\Taxonomies\Categories;
use App\Query\CraftCMS\Taxonomies\Tags;
use DateTimeImmutable;
use Exception;
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
     * @Route("/blog/feed")
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

        return $this->blogFeeds($manager, $site, $feedUrl, $pageUrl, $twig);
    }

    /**
     * @Route("/blog/category/{slug}/feed", requirements={"slug": "[^/]+"})
     *
     * @param QueryManager $manager
     * @param string       $slug
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
    public function category(QueryManager $manager, string $slug, Site $site, Environment $twig): Response
    {
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

        $feedUrl = $this->generateUrl('app_feed_category', ['slug' => $slug], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_blog_category', ['slug' => $slug], UrlGeneratorInterface::ABSOLUTE_URL);

        $manager->add('rss', new Blog($site->siteId, self::LIMIT, $category['id']));

        return $this->blogFeeds($manager, $site, $feedUrl, $pageUrl, $twig);
    }

    /**
     * @Route("/blog/tags/{slug}/feed", requirements={"slug": "[^/]+"})
     *
     * @param QueryManager $manager
     * @param string       $slug
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
    public function tag(QueryManager $manager, string $slug, Site $site, Environment $twig): Response
    {
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

        $feedUrl = $this->generateUrl('app_feed_tag', ['slug' => $slug], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_blog_tag', ['slug' => $slug], UrlGeneratorInterface::ABSOLUTE_URL);

        $manager->add('rss', new Blog($site->siteId, self::LIMIT, null, $tag['id']));

        return $this->blogFeeds($manager, $site, $feedUrl, $pageUrl, $twig);
    }

    /**
     * @Route("/news/feed")
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

        return $this->prNewsFeeds($site, $pageUrl, $feedUrl, $entries, $twig);
    }

    /**
     * @Route("/press-releases/feed")
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
    public function pressReleases(QueryManager $manager, Site $site, Environment $twig): Response
    {
        $manager->add('rss', new PressReleases($site->siteId, self::LIMIT));
        $entries = $manager->getCollection('rss');

        $feedUrl = $this->generateUrl('app_feed_pressreleases', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $pageUrl = $this->generateUrl('app_pressreleases_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->prNewsFeeds($site, $pageUrl, $feedUrl, $entries, $twig);
    }

    /**
     * @param QueryManager $manager
     * @param Site         $site
     * @param string       $feedUrl
     * @param string       $pageUrl
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
    private function blogFeeds(
        QueryManager $manager,
        Site $site,
        string $feedUrl,
        string $pageUrl,
        Environment $twig
    ): Response {
        $entries = $manager->getCollection('rss');
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

        $feed = new Feed();
        $feed->setTitle("W3C");
        $feed->setLanguage($site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription('Description');

        foreach ($entries as $post) {
            [$url, $entry] = $this->entry($post, $feed, $twig);
            $entry->setCommentLink($url . '#comments');
            if (array_key_exists($post['id'], $commentCounts)) {
                $entry->setCommentCount($commentCounts[$post['id']]);
            }
            $feed->addEntry($entry);
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @param Site        $site
     * @param string      $pageUrl
     * @param string      $feedUrl
     * @param Collection  $entries
     * @param Environment $twig
     *
     * @return Response
     * @throws InvalidLocaleException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function prNewsFeeds(
        Site $site,
        string $pageUrl,
        string $feedUrl,
        Collection $entries,
        Environment $twig
    ): Response {
        $feed = new Feed();
        $feed->setTitle("W3C");
        $feed->setLanguage($site->getLocale());

        $feed->setLink($pageUrl);
        $feed->setFeedLink($feedUrl, 'rss');

        $feed->setDateModified(time());
        $feed->setDescription('Description');

        foreach ($entries as $post) {
            [$url, $entry] = $this->entry($post, $feed, $twig);

            $feed->addEntry($entry);
        }

        $out = $feed->export('rss');

        return new Response($out, Response::HTTP_OK, ['content-type' => 'application/rss+xml']);
    }

    /**
     * @param              $post
     * @param Feed         $feed
     * @param Environment  $twig
     *
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function entry($post, Feed $feed, Environment $twig): array
    {
        $date  = new DateTimeImmutable($post['date']);
        $url   = $this->generateUrl(
            'app_blog_show',
            ['year' => $date->format('Y'), 'slug' => $post['slug']],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $entry = $feed->createEntry();
        $entry->setTitle($post['title']);
        $entry->setLink($url);

        $authors = implode(
            ', ',
            array_map(function ($e) {
                return $e['name'];
            }, $post['authors'])
        );
        if ($authors) {
            $entry->addAuthor(['name'  => $authors]);
        }
        //$entry->setDateModified(time());
        $entry->setDateCreated($date);
        if ($post['excerpt']) {
            $entry->setDescription($post['excerpt']);
        }

        if (count($post['defaultFlexibleComponents']) > 0) {
            $entry->setContent(
                $twig->render('rss_entry.html.twig', ['components' => $post['defaultFlexibleComponents']])
            );
        }

        return [$url, $entry];
    }
}
