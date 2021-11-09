<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Home;

use App\Service\CraftCMS;
use DateTimeImmutable;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\DateTimeValue;
use Symfony\Component\Routing\RouterInterface;

class RecentActivities extends GraphQLQuery
{
    private RouterInterface $router;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(int $siteId, RouterInterface $router, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->router = $router;
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/home/recent-activities.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/thumbnailImage.graphql')
             ->setRootPropertyPath('[recentEntries]')
             ->addParam('siteId', $siteId)
             ->cache($cacheLifetime);
    }

    public function getMapping()
    {
        return [
            '[category]'         => '[sectionHandle]',
            '[title]'            => '[title]',
            '[text]'             => '[excerpt]',
            '[img]'              => '[thumbnailImage][0]',
            '[thumbnailAltText]' => '[thumbnailAltText]',
            '[url]'              => new CallableData(
                [$this, 'transformEntryUri'],
                '[sectionHandle]',
                '[slug]',
                '[year]'
            )
        ];
    }

    public function transformEntryUri(string $sectionHandle, string $slug, string $year): ?string
    {
        switch ($sectionHandle) {
            case 'blogPosts':
                $route = 'app_blog_show';
                break;
            case 'pressReleases':
                $route = 'app_pressreleases_show';
                break;
            case 'newsArticles':
                $route = 'app_news_show';
                break;
            default:
                return null;
        }

        return $this->router->generate($route, ['slug' => $slug, 'year' => $year]);
    }
}
