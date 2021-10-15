<?php

namespace App\Query\CraftCMS\Taxonomies;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Query\GraphQLQuery;

class CategoryInfo extends GraphQLQuery
{
    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int    $siteId        Site ID of page content
     * @param string $handle        Taxonomy handle
     * @param string $slug
     * @param int    $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(int $siteId, string $handle, string $slug, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/taxonomies/category-info.graphql')
            ->addVariable('siteId', $siteId)
            ->addVariable('handle', $handle)
            ->addVariable('slug', $slug)
            ->setRootPropertyPath('[category]')
            ->cache($cacheLifetime)
        ;
    }
}
