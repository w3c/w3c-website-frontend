<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\News;

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

    public function __construct(
        int $siteId,
        int $year,
        string $slug,
        RouterInterface $router,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/news/entry.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/defaultFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/seoData.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/contentImage.graphql')
            ->setRootPropertyPath('[entry]')

            ->addVariable('siteId', $siteId)
            ->addVariable('year', ['and', '>=' . $year, '<' . ($year + 1)])
            ->addVariable('slug', $slug)
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping()
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('ecosystems', $this->mapTaxonomy('ecosystems', 'transformEcosystem'));

        return $mapping;
    }

    private function mapTaxonomy(string $field, string $function): array
    {
        return [
            '[' . $field . ']' => new MapArray(
                '[' . $field . ']',
                [
                    '[title]' => '[title]',
                    '[slug]'  => '[slug]',
                    '[url]'   => new CallableData([$this, $function])
                ]
            )
        ];
    }

    public function transformEcosystem(array $data): string
    {
        $slug = $data['slug'];

        return $this->router->generate('app_ecosystem_show', ['slug' => $slug]);
    }
}
