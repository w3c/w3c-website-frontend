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
     * @param string $siteHandle Site Handle of page content
     * @param string $handle        Taxonomy handle
     * @param int    $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(string $siteHandle, string $handle, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/taxonomies/tags.graphql')
             ->addVariable('site', $siteHandle)
             ->addVariable('handle', $handle)
             ->setRootPropertyPath('[tags]')
             ->cache($cacheLifetime)
        ;
    }
}
