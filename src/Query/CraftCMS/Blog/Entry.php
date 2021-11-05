<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Blog;

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
     * @param int             $siteId        Site ID of page content
     * @param int             $year
     * @param string          $slug
     * @param RouterInterface $router
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        int $siteId,
        int $year,
        string $slug,
        RouterInterface $router,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/blog/entry.graphql')
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
        $mapping->addMapping('category', ['[category]' => new CallableData([$this, 'mapCategory'], '[category]')]);
        $mapping->addMapping('tags', $this->mapTaxonomy('tags', 'transformTag'));
        $mapping->addMapping('ecosystems', $this->mapTaxonomy('ecosystems', 'transformEcosystem'));
        $mapping->addMapping('localized', [
            '[localized]' => new MapArray('[localized]', [
                '[title]' => '[title]',
                '[language_code]' => '[language_code]',
                '[url]' => new CallableData(
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
        return $this->router->generate('app_blog_show', [
            'year'    => $year,
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

    public function mapCategory(array $data): array
    {
        if (count($data) > 0) {
            return [
                'url'   => $this->router->generate('app_blog_category', ['slug' => $data[0]['slug']]),
                'title' => $data[0]['title']
            ];
        }

        return [];
    }

    public function transformEcosystem(array $data): string
    {
        $slug = $data['slug'];
        return $this->router->generate('app_ecosystem_show', ['slug' => $slug]);
    }

    public function transformTag(array $data): string
    {
        $slug = $data['slug'];

        return $this->router->generate('app_blog_tag', ['slug' => $slug]);
    }
}
