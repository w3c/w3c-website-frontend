<?php

namespace App\Query\CraftCMS\Taxonomies;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Query\GraphQLQuery;

class GroupInfo extends GraphQLQuery
{
    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int    $siteId        Site ID of page content
     * @param string $type
     * @param string $slug
     * @param int    $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(int $siteId, string $type, string $slug, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/taxonomies/group-info.graphql')
             ->addVariable('siteId', $siteId)
             ->addVariable('type', $type)
             ->addVariable('slug', $slug)
             ->setRootPropertyPath('[category]')
             ->cache($cacheLifetime);
    }
}
