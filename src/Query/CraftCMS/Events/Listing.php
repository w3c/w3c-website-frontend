<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Events;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\DateTimeValue;
use Symfony\Component\Routing\RouterInterface;

class Listing extends GraphQLQuery
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
     * @param int             $siteId        Site ID of page content
     * @param int|null        $category
     * @param int|null        $tag
     * @param string|null     $before
     * @param string|null     $after
     * @param string|null     $search
     * @param int             $limit
     * @param int             $page
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        int $siteId,
        int $category = null,
        int $tag = null,
        string $before = null,
        string $after = null,
        string $search = null,
        int $limit = 10,
        int $page = 1,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/events/listing.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/listingEvent.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/listingExternalEvent.graphql')
            ->setRootPropertyPath('[entries]')
            ->setTotalResults('[total]')
            ->setResultsPerPage($limit)
            ->setCurrentPage($page)

            ->addVariable('siteId', $siteId)
            ->addVariable('category', $category)
            ->addVariable('tag', $tag)
            ->addVariable('before', $before)
            ->addVariable('after', $after)
            ->addVariable('search', $search)
            ->addVariable('limit', $limit)
            ->addVariable('offset', ($page - 1) * $limit)
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping()
    {
        return [
            '[id]'               => '[id]',
            '[slug]'             => '[slug]',
            '[url]'              => new CallableData([$this, 'transformEventUrl']),
            '[title]'            => '[title]',
            '[start]'            => new DateTimeValue('[start]'),
            '[end]'              => new DateTimeValue('[end]'),
            '[tz]'               => '[tz]',
            '[category]'         => new CallableData([$this, 'transformCategory'], '[category][0]'),
            '[type]'             => '[type][0]',
            '[excerpt]'          => '[excerpt]',
            '[thumbnailImage]'   => '[thumbnailImage][0]',
            '[thumbnailAltText]' => '[thumbnailAltText]',
            '[location]'         => '[location]',
            '[host]'             => '[host]',
        ];
    }

    public function transformEventUrl(array $data): string
    {
        if (array_key_exists('urlLink', $data) && $data['urlLink']) {
            return $data['urlLink'];
        }

        return $this->router->generate('app_events_show', [
            'type' => $data['type'][0]['slug'],
            'slug' => $data['slug'],
            'year' => $data['year']
        ]);
    }

    public function transformCategory(?array $data): ?array
    {
        if ($data) {
            return [
                'id' => $data['id'],
                'slug' => $data['slug'],
                'title' => $data['title'],
                'url' => $this->router->generate('app_events_category', ['slug' => $data['slug']])
            ];
        }

        return null;
    }
}
