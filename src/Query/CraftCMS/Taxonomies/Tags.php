<?php

namespace App\Query\CraftCMS\Taxonomies;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Query\GraphQLQuery;

class Tags extends GraphQLQuery
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
     * @param int    $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(int $siteId, string $handle, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/taxonomies/tags.graphql')
             ->addVariable('siteId', $siteId)
             ->addVariable('handle', $handle)
             ->setRootPropertyPath('[tags]')
             ->enableCache($cacheLifetime)//->setCacheTags($uri)
        ;
    }
}
