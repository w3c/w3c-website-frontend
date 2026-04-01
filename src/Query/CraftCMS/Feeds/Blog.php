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

    public function __construct(
        string $siteHandle,
        int $limit,
        ?int $category = null,
        ?int $tag = null
    ) {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/feeds/blog.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/defaultFlexibleComponents.graphql')
            ->setRootPropertyPath('[entries]')
            ->addVariable('site', $siteHandle)
            ->addVariable('limit', $limit)
            ->addVariable('category', $category)
            ->addVariable('tag', $tag)
            ->cacheTags(['blogPosts'])
        ;
    }
}
