<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;

class BlogListing extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int         $siteId        Site ID of page content
     * @param int|null    $category
     * @param string|null $before
     * @param string|null $after
     * @param string|null $search
     * @param int         $limit
     * @param int         $page
     * @param int         $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        int $siteId,
        int $category = null,
        string $before = null,
        string $after = null,
        string $search = null,
        int $limit = 10,
        int $page = 1,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->setGraphQLFromFile(__DIR__ . '/graphql/blogListing.graphql')
            ->setRootPropertyPath('[entries]')
            ->setTotalResults('[total]')
            ->setResultsPerPage($limit)
            ->setCurrentPage($page)

            ->addVariable('siteId', $siteId)
            ->addVariable('category', $category)
            ->addVariable('before', $before)
            ->addVariable('after', $after)
            ->addVariable('search', $search)
            ->addVariable('limit', $limit)
            ->addVariable('offset', ($page - 1) * $limit)
            ->enableCache($cacheLifetime)
            //->setCacheTags($uri)
        ;
    }
}
