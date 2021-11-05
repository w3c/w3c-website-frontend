<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Ecosystems;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;

/**
 * Get global navigation
 */
class Ecosystem extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int    $siteId        Site ID of page content
     * @param string $slug          ecosystem's slug
     * @param int    $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(int $siteId, string $slug, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/ecosystems/ecosystem.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/ecosystemsFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/ecosystemsBottomFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/contentImage.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/seoData.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/breadcrumbs.graphql')
            ->setRootPropertyPath('[entry]')
            ->addVariable('slug', $slug)
            ->addVariable('siteId', $siteId)
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping()
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('heroIllustration', ['[heroIllustration]' => '[heroIllustration][0]']);
        $mapping->addMapping('ecosystem', ['[taxonomy-slug]' => '[ecosystem][0][slug]']);
        $mapping->addMapping('ecosystem', ['[taxonomy-id]' => '[ecosystem][0][id]']);

        return $mapping;
    }
}
