<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Ecosystems;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\Routing\RouterInterface;

/**
 * Get global navigation
 */
class Ecosystem extends GraphQLQuery
{
    public RouterInterface $router;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param RouterInterface $router
     * @param string $siteHandle Site Handle of page content
     * @param string          $slug          ecosystem's slug
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        string $siteHandle,
        string $slug,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/ecosystems/ecosystem.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/ecosystemsFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/ecosystemsBottomFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/contentImage.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/breadcrumbs.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/thumbnailImage.graphql')
            ->setRootPropertyPath('[entry]')
            ->addVariable('slug', $slug)
            ->addVariable('site', $siteHandle)
            ->cache($cacheLifetime)
            ->cacheTags(['ecosystems'])
        ;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('heroIllustration', ['[heroIllustration]' => '[heroIllustration][0]']);
        $mapping->addMapping('ecosystem', ['[taxonomy-slug]' => '[ecosystem][0][slug]']);
        $mapping->addMapping('ecosystem', ['[taxonomy-id]' => '[ecosystem][0][id]']);
        $mapping->addMapping('localized', [
            '[localized]' => new MapArray('[localized]', [
                '[title]'         => '[title]',
                '[language_code]' => '[language_code]',
                '[url]'           => new CallableData([$this, 'transformLocalizedUrl'], '[language_code]', '[slug]')
            ])
        ]);

        return $mapping;
    }

    public function transformLocalizedUrl(string $lang, string $slug)
    {
        return $this->router->generate('app_ecosystem_show', ['slug' => $slug, '_locale' => strtolower($lang)]);
    }
}
