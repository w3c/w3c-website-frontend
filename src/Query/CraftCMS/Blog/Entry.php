<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Blog;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;

class Entry extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int    $siteId        Site ID of page content
     * @param string $slug
     * @param int    $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        int $siteId,
        string $slug,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/blog/entry.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/defaultFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/seoData.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/contentImage.graphql')
            ->setRootPropertyPath('[entry]')

            ->addVariable('siteId', $siteId)
            ->addVariable('slug', $slug)
            ->enableCache($cacheLifetime)
            //->setCacheTags($uri)
        ;
    }
}
