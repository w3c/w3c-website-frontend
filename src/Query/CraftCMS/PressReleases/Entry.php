<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\PressReleases;

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
        string $siteHandle,
        int $year,
        string $slug,
        RouterInterface $router,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/press-releases/entry.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/defaultFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/contentImage.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/thumbnailImage.graphql')
            ->setRootPropertyPath('[entry]')

            ->addVariable('site', $siteHandle)
            ->addVariable('year', ['and', '>=' . $year, '<' . ($year + 1)])
            ->addVariable('slug', $slug)
            ->cache($cacheLifetime)
            ->cacheTags(['pressReleases'])
        ;
    }

    public function getMapping()
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('ecosystems', $this->mapTaxonomy('ecosystems', 'transformEcosystem'));
        $mapping->addMapping('localized', [
            '[localized]' => new MapArray('[localized]', [
                '[title]'         => '[title]',
                '[language_code]' => '[language_code]',
                '[url]'           => new CallableData(
                    [$this, 'transformLocalizedUrl'],
                    '[language_code]',
                    '[year]',
                    '[slug]'
                )
            ])
        ]);

        return $mapping;
    }

    public function transformLocalizedUrl(string $lang, string $year, string $slug)
    {
        return $this->router->generate('app_pressreleases_show', [
            'year'    => $year,
            'slug'    => $slug,
            '_locale' => strtolower($lang)
        ]);
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
