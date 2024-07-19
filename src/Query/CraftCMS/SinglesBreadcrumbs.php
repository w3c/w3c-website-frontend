<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapItem;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\CallableValue;
use Symfony\Component\Routing\RouterInterface;

class SinglesBreadcrumbs extends GraphQLQuery
{
    private RouterInterface $router;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param RouterInterface $router
     * @param string $siteHandle Site Handle of page content
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(RouterInterface $router, string $siteHandle, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->router = $router;
        $this->setGraphQLFromFile(__DIR__ . '/graphql/singles-breadcrumbs.graphql')
            ->addVariable('site', $siteHandle)
            ->cache($cacheLifetime)
            ->cacheTags(
                [
                    'homepage',
                    'blogListing',
                    'pressReleasesListing',
                    'eventsListing',
                    'newsListing',
                    'ecosystemsLandingPage'
                ]
            );
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[homepage]' => new CallableData([$this, 'transformHomepage'], '[homepage]'),
            '[blog]' => new CallableData([$this, 'transformBlog'], '[blog]'),
            '[pressReleases]' => new CallableData([$this, 'transformPressReleases'], '[pressReleases]'),
            '[events]' => new CallableData([$this, 'transformEvents'], '[events]'),
            '[news]' => new CallableData([$this, 'transformNews'], '[news]'),
            '[ecosystems]' => new CallableData([$this, 'transformEcosystem'], '[ecosystems]'),
        ];
    }

    public function transformHomepage(?array $data): array
    {
        if ($data) {
            return [
                'title' => $data['title'],
                'url'   => $this->router->generate('app_default_home')
            ];
        }

        return [];
    }

    public function transformBlog(?array $data): array
    {
        if ($data) {
            return [
                'title' => $data['title'],
                'url'   => $this->router->generate('app_blog_index')
            ];
        }

        return [];
    }

    public function transformPressReleases(?array $data): array
    {
        if ($data) {
            return [
                'title' => $data['title'],
                'url'   => $this->router->generate('app_pressreleases_index')
            ];
        }

        return [];
    }

    public function transformEvents(?array $data): array
    {
        if ($data) {
            return [
                'title' => $data['title'],
                'url'   => $this->router->generate('app_events_index')
            ];
        }

        return [];
    }

    public function transformNews(?array $data): array
    {
        if ($data) {
            return [
                'title' => $data['title'],
                'url'   => $this->router->generate('app_news_index')
            ];
        }

        return [];
    }

    public function transformEcosystem(?array $data): array
    {
        if ($data) {
            return [
                'title' => $data['title'],
                'url'   => $this->router->generate('app_default_index', ['route' => 'ecosystems'])
            ];
        }

        return [];
    }
}
