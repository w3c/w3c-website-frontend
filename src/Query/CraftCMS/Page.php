<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\Routing\RouterInterface;

/**
 * Get global navigation
 */
class Page extends GraphQLQuery
{
    private RouterInterface $router;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param RouterInterface $router
     * @param int $siteId Site ID of page content
     * @param string $uri Page URI to return
     * @param int $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        int $siteId,
        string $uri,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;
        $this->setGraphQLFromFile(__DIR__ . '/graphql/page.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/defaultFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/landingFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/contentImage.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/thumbnailImage.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/seoData.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/breadcrumbs.graphql')
            ->setRootPropertyPath('[entry]')

            // Set page URI to retrieve navigation for
            ->addVariable('uri', $uri)

            // Set site ID to retrieve navigation for
            ->addVariable('siteId', $siteId)

            // Caching
            ->doNotCache()
        ;
    }

    public function getMapping()
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('heroIllustration', ['[heroIllustration]' => '[heroIllustration][0]']);
        $mapping->addMapping('siblingNavigation', [
            '[siblings]' => new CallableData([$this, 'mapSiblings'], '[siblingNavigation][siblings]')
        ]);

        return $mapping;
    }

    public function mapSiblings(array $data): array
    {
        $siblings = [];
        foreach ($data as $sibling) {
            $siblings[] = [
                'title' => $sibling['title'],
                'url'   => $this->router->generate('app_default_index', ['route' => $sibling['uri']])
            ];
        }

        return $siblings;
    }
}
