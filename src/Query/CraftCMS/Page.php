<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;

/**
 * Get global navigation
 */
class Page extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int $siteId Site ID of page content
     * @param string $uri Page URI to return
     * @param int $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     * @throws \Strata\Data\Exception\GraphQLQueryException
     */
    public function __construct(int $siteId, string $uri, int $cacheLifetime = CacheLifetime::HOUR)
    {
        $this->setGraphQLFromFile(__DIR__ . '/graphql/page.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/defaultFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/landingFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/contentImage.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/seoData.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/breadcrumbs.graphql')
            ->setRootPropertyPath('[entry]')

            // Set page URI to retrieve navigation for
            ->addVariable('uri', $uri)

            // Set site ID to retrieve navigation for
            ->addVariable('siteId', $siteId)

            // Cache page response
            ->enableCache($cacheLifetime)
            //->setCacheTags($uri)
        ;
    }

    public function getMapping()
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('siblingNavigation', ['[siblings]' => '[siblingNavigation][siblings]']);
        $mapping->addMapping('heroIllustration', ['[heroIllustration]' => '[heroIllustration][0]']);

        return $mapping;
    }
}
