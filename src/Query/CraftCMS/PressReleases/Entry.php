<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\PressReleases;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Query\GraphQLQuery;

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
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/press-releases/entry.graphql')
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
