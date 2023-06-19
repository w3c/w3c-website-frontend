<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Events;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Query\GraphQLQuery;

class Page extends GraphQLQuery
{
    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param string $siteHandle Site Handle of page content
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        string $siteHandle,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/events/page.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/breadcrumbs.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/thumbnailImage.graphql')
            ->setRootPropertyPath('[entry]')

            ->addVariable('site', $siteHandle)
            ->cache($cacheLifetime)
            ->cacheTags(['events'])
        ;
    }
}
