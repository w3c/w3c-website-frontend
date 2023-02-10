<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Blog;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Query\GraphQLQuery;

class Comments extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    public function __construct(int $id, int $siteId, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/blog/comments.graphql')
            ->setRootPropertyPath('[comments]')
            ->addVariable('postId', $id)
            ->addVariable('siteId', $siteId)
            ->cache($cacheLifetime)
        ;
    }
}
