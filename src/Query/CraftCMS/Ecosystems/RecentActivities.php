<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Ecosystems;

use App\Service\CraftCMS;
use DateTimeImmutable;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\MappingStrategyInterface;
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
     * @param string $siteHandle
     * @param int $ecosystemId   Ecosystem ID to get recent activities for
     * @param int $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        string $siteHandle,
        int $ecosystemId,
        RouterInterface $router,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;
        // ISO 8601 date
        $recentEventsEndDate = '>' . (new DateTimeImmutable())->format('c');
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/ecosystems/recent-activities.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/thumbnailImage.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/listingEvent.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/listingExternalEvent.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/listingPageEvent.graphql')
             ->addVariable('site', $siteHandle)
             ->addVariable('ecosystemId', $ecosystemId)
             ->addVariable('endDatetime', $recentEventsEndDate)
             ->cache($cacheLifetime)
             ->cacheTags(['blogPosts', 'newsArticles', 'pressReleases'])
        ;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[recentEntries]' => new MapArray('[recentEntries]', [
                '[category]'    => '[sectionHandle]',
                '[title]'            => '[title]',
                '[text]'          => '[excerpt]',
                '[img]'   => '[thumbnailImage][0]',
                '[thumbnailAltText]' => '[thumbnailAltText]',
                '[url]'              => new CallableData(
                    [$this, 'transformEntryUri'],
                    '[sectionHandle]',
                    '[slug]',
                    '[year]'
                )
            ]),
            '[recentEvents]' => new MapArray('[recentEvents]', [
                '[id]'               => '[id]',
                '[slug]'             => '[slug]',
                '[url]'              => new CallableData([$this, 'transformEventUrl']),
                '[title]'            => '[title]',
                '[start]'            => new DateTimeValue('[start]'),
                '[end]'              => new DateTimeValue('[end]'),
                '[tz]'               => '[tz]',
                '[category]'         => new CallableData([$this, 'transformEventCategory'], '[category][0]'),
                '[type]'             => '[type][0]',
                '[excerpt]'          => '[excerpt]',
                '[thumbnailImage]'   => '[thumbnailImage][0]',
                '[thumbnailAltText]' => '[thumbnailAltText]',
                '[location]'         => '[location]',
                '[host]'             => '[host]',
            ])
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

    public function transformEventUrl(array $data): string
    {
        switch ($data['typeHandle']) {
            case 'external':
                return $data['urlLink'];
            case 'entryContentIsACraftPage':
                return $this->router->generate('app_default_index', ['route' => $data['page'][0]['uri']]);
            default:
                return $this->router->generate('app_events_show', [
                    'type' => $data['type'][0]['slug'],
                    'slug' => $data['slug'],
                    'year' => $data['year']
                ]);
        }
    }

    public function transformEventCategory(?array $data): ?array
    {
        if ($data) {
            return [
                'id'    => $data['id'],
                'slug'  => $data['slug'],
                'title' => $data['title'],
                'url'   => $this->router->generate('app_events_index', ['category' => $data['slug']])
            ];
        }

        return null;
    }
}
