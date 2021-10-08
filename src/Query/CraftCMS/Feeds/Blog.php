<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Feeds;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;

class Blog extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Query to retrieve blog posts for feeds
     *
     * @param int      $siteId Site ID of page content
     * @param int      $limit
     * @param int|null $category
     * @param int|null $tag
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        int $siteId,
        int $limit,
        int $category = null,
        int $tag = null
    ) {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/feeds/blog.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/defaultFlexibleComponents.graphql')
            ->setRootPropertyPath('[entries]')
            ->addVariable('siteId', $siteId)
            ->addVariable('limit', $limit)
            ->addVariable('category', $category)
            ->addVariable('tag', $tag)
        ;
    }
}
