<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\Routing\RouterInterface;

class Page extends GraphQLQuery
{
    private RouterInterface $router;
    private array $breadcrumbsRoot;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param RouterInterface $router
     * @param array           $breadcrumbsRoot
     * @param string $siteHandle Site Handle of page content
     * @param string          $uri           Page URI to return
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        array $breadcrumbsRoot,
        string $siteHandle,
        string $uri,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router          = $router;
        $this->breadcrumbsRoot = $breadcrumbsRoot;
        $this->setGraphQLFromFile(__DIR__ . '/graphql/page.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/defaultFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/landingFlexibleComponents.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/contentImage.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/thumbnailImage.graphql')
            ->addFragmentFromFile(__DIR__ . '/graphql/fragments/breadcrumbs.graphql')
            ->setRootPropertyPath('[entry]')

            // Set page URI to retrieve navigation for
            ->addVariable('uri', $uri)

            // Set site ID to retrieve navigation for
            ->addVariable('site', $siteHandle)

            // Caching
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('heroIllustration', ['[heroIllustration]' => '[heroIllustration][0]']);
        $mapping->addMapping('siblingNavigation', [
            '[siblings]' => new CallableData([$this, 'mapSiblings'], '[siblingNavigation][siblings]')
        ]);
        $mapping->addMapping('breadcrumbs', [
            '[breadcrumbs]' => new CallableData([$this, 'mapBreadcrumbs'], '[breadcrumbs]', '[title]', '[uri]')
        ]);
        $mapping->addMapping('localized', ['[localized]' => new MapArray('[localized]', [
            '[title]' => '[title]',
            '[language_code]' => '[language_code]',
            '[url]' => new CallableData([$this, 'transformLocalizedUrl'], '[language_code]', '[uri]')
        ])]);

        return $mapping;
    }

    public function transformLocalizedUrl(string $lang, string $uri)
    {
        return $this->router->generate('app_default_index', ['route' => $uri, '_locale' => strtolower($lang)]);
    }

    public function mapSiblings(?array $data): array
    {
        $siblings = [];
        if ($data) {
            foreach ($data as $sibling) {
                $siblings[] = [
                    'title' => $sibling['title'],
                    'url'   => $this->router->generate('app_default_index', ['route' => $sibling['uri']])
                ];
            }
        }

        return $siblings;
    }

    public function mapBreadcrumbs(?array $breadcrumbs, string $title, string $uri): array
    {
        return [
            'title'  => $title,
            'url'    => $this->router->generate('app_default_index', ['route' => $uri]),
            'parent' => $this->mapBreadcrumbsRecursive($breadcrumbs)
        ];
    }

    private function mapBreadcrumbsRecursive(?array $breadcrumbs): array
    {
        if (!$breadcrumbs) {
            return $this->breadcrumbsRoot;
        }

        return [
            'title'  => $breadcrumbs['title'],
            'url'    => $this->router->generate('app_default_index', ['route' => $breadcrumbs['uri']]),
            'parent' => $this->mapBreadcrumbsRecursive($breadcrumbs['parent'])

        ];
    }
}
