<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Feeds;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;

class Comments extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Query to retrieve comment IDs of blog posts passed as parameters
     *
     * @param int[] $postIds IDs of blog posts to retrieve comments for
     *
     * @throws GraphQLQueryException
     */
    public function __construct(array $postIds)
    {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/feeds/comments.graphql')
            ->setRootPropertyPath('[comments]')
            ->addVariable('ownerId', $postIds)
        ;
    }
}
