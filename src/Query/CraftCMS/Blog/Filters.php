<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Blog;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Filters extends GraphQLQuery
{
    private RouterInterface $router;
    private TranslatorInterface $translator;

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
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router     = $router;
        $this->translator = $translator;
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/blog/filters.graphql')
            ->addVariable('site', $siteHandle)
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[categories]' => new CallableData([$this, 'transformCategories'], '[categories]'),
            '[archives]'   => new CallableData([$this, 'transformArchives'], '[first][year]', '[last][year]')
        ];
    }

    public function transformCategories(array $categories): array
    {
        $result = [
            [
                'title' => $this->translator->trans('listing.blog.filters.all', [], 'w3c_website_templates_bundle'),
                'url'   => $this->router->generate('app_blog_index'),
            ]
        ];
        foreach ($categories as $category) {
            $result[$category['slug']] = [
                'title' => $category['title'],
                'url'   => $this->router->generate('app_blog_category', ['slug' => $category['slug']])
            ];
        }

        return $result;
    }

    public function transformArchives(string $first = null, string $last = null): array
    {
        if (!$first) {
            return [];
        }

        $archives = [
            [
                'title' => $this->translator->trans('listing.blog.filters.all', [], 'w3c_website_templates_bundle'),
                'url'   => $this->router->generate('app_blog_index')
            ]
        ];
        for ($year = $last; $year >= $first; $year--) {
            $archives[] = [
                'title' => $year,
                'url'   => $this->router->generate('app_blog_archive', ['year' => $year])
            ];
        }

        return $archives;
    }
}
