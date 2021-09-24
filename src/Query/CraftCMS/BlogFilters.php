<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;

class BlogFilters extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int $siteId        Site ID of page content
     * @param int $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(int $siteId, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->setGraphQLFromFile(__DIR__ . '/graphql/blogFilters.graphql')
            ->addVariable('siteId', $siteId)
            ->enableCache($cacheLifetime)
            //->setCacheTags($uri)
        ;
    }
}
