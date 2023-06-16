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
use Strata\Data\Transform\Value\DateTimeValue;
use Symfony\Component\Routing\RouterInterface;

class Entry extends GraphQLQuery
{
    private RouterInterface $router;
    private array $type;
    private int $year;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param string $siteHandle Site Handle of page content
     * @param array          $type
     * @param int             $year
     * @param string          $slug
     * @param RouterInterface $router
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        string $siteHandle,
        array $type,
        int $year,
        string $slug,
        RouterInterface $router,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;
        $this->year   = $year;
        $this->type   = $type;

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/events/entry.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/defaultFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/contentImage.graphql')
            ->setRootPropertyPath('[entry]')

            ->addVariable('site', $siteHandle)
            ->addVariable('type', $type['id'])
            ->addVariable('start', ['and', '>=' . $year, '<' . ($year+1)])
            ->addVariable('slug', $slug)
            ->cache($cacheLifetime)
            ->cacheTags(['events'])
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
        $mapping->addMapping('start', ['[start]' => new DateTimeValue('[start]')]);
        $mapping->addMapping('end', ['[end]' => new DateTimeValue('[end]')]);
        $mapping->addMapping('postDate', ['[postDate]' => new DateTimeValue('[postDate]')]);
        $mapping->addMapping('dateUpdated', ['[dateUpdated]' => new DateTimeValue('[dateUpdated]')]);
        $mapping->addMapping('localized', [
            '[localized]' => new MapArray('[localized]', [
                '[title]'         => '[title]',
                '[language_code]' => '[language_code]',
                '[url]'           => new CallableData(
                    [$this, 'transformLocalizedUrl'],
                    '[language_code]',
                    '[slug]',
                )
            ])
        ]);

        return $mapping;
    }

    public function transformLocalizedUrl(string $lang, string $slug)
    {
        return $this->router->generate('app_events_show', [
            'type'    => $this->type['slug'],
            'year'    => $this->year,
            'slug'    => $slug,
            '_locale' => strtolower($lang)
        ]);
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
