<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Events;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\Routing\RouterInterface;

class Entry extends GraphQLQuery
{

    private RouterInterface $router;

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
        string $type,
        int $year,
        string $slug,
        RouterInterface $router,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/events/entry.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/defaultFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/seoData.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/contentImage.graphql')
            ->setRootPropertyPath('[entry]')

            ->addVariable('siteId', $siteId)
            ->addVariable('type', $type)
            ->addVariable('start', ['and', '>=' . $year, '<' . ($year+1)])
            ->addVariable('slug', $slug)
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping()
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('categories', $this->mapTaxonomy('categories', 'transformCategory'));
        $mapping->addMapping('tags', $this->mapTaxonomy('tags', 'transformTag'));
        $mapping->addMapping('ecosystems', $this->mapTaxonomy('ecosystems', 'transformEcosystem'));
        $mapping->addMapping('type', ['[type]' => '[type][0]']);
        $mapping->addMapping('website', ['[website]' => '[website][0]']);

        return $mapping;
    }

    private function mapTaxonomy(string $field, string $function): array
    {
        return ['[' . $field . ']' => new MapArray(
            '[' . $field . ']',
            [
                '[title]' => '[title]',
                '[slug]'  => '[slug]',
                '[url]'   => new CallableData([$this, $function])
            ]
        )];
    }

    public function transformCategory(array $data): string
    {
        $slug = $data['slug'];

        return $this->router->generate('app_events_index', ['category' => $slug]);
    }

    public function transformEcosystem(array $data): string
    {
        $slug = $data['slug'];
        return $this->router->generate('app_ecosystem_show', ['slug' => $slug]);
    }

    public function transformTag(array $data): string
    {
        $slug = $data['slug'];

        return $this->router->generate('app_events_index', ['tag' => $slug]);
    }
}
