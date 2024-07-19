<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Events;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Filters extends GraphQLQuery
{
    private RouterInterface $router;
    private TranslatorInterface $translator;
    private ?string $type = null;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     * @param string $siteHandle Site Handle of page content
     * @param int                 $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator,
        string $siteHandle,
        ?string $type = null,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router     = $router;
        $this->translator = $translator;
        $this->type       = $type;
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/events/filters.graphql')
            ->addVariable('site', $siteHandle)
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[categories]' => new CallableData([$this, 'transformCategories'], '[categories]'),
            '[types]'      => new CallableData([$this, 'transformTypes'], '[types]'),
            '[archives]'   => new CallableData([$this, 'transformArchives'], '[first][year]', '[last][year]')
        ];
    }

    public function transformTypes(array $types): array
    {
        $result = [
            [
                'title'  => $this->translator->trans('listing.events.filters.all', [], 'w3c_website_templates_bundle'),
                'slug'   => null
            ]
        ];
        foreach ($types as $type) {
            $result[] = [
                'id'       => $type['id'],
                'title'    => $type['title'],
                'slug'     => $type['slug'],
                'pageLead' => $type['pageLead']
            ];
        }

        return $result;
    }

    public function transformCategories(array $categories): array
    {
        $result = [
            [
                'title' => $this->translator->trans('listing.events.filters.all', [], 'w3c_website_templates_bundle'),
                'slug'  => null
            ]
        ];
        foreach ($categories as $category) {
            $result[$category['slug']] = [
                'title' => $category['title'],
                'slug'  => $category['slug']
            ];
        }

        return $result;
    }

    public function transformArchives(string $first = null, string $last = null): array
    {
        if (!$first) {
            return [];
        }

        if ($this->type) {
            $routeIndex    = 'app_events_index_type';
            $routeArchives = 'app_events_archive_type';
            $params        = ['type' => $this->type];
        } else {
            $routeIndex    = 'app_events_index';
            $routeArchives = 'app_events_archive';
            $params        = [];
        }

        $archives = [
            [
                'title' => $this->translator->trans(
                    'listing.events.filters.upcoming',
                    [],
                    'w3c_website_templates_bundle'
                ),
                'url'   => $this->router->generate($routeIndex, $params)
            ]
        ];

        for ($year = $last; $year >= $first; $year--) {
            $archives[] = [
                'title' => $year,
                'url'   => $this->router->generate($routeArchives, array_merge(['year' => $year], $params))
            ];
        }

        return $archives;
    }
}
