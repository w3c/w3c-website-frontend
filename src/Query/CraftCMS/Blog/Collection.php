<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Blog;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\DateTimeValue;
use Symfony\Component\Routing\RouterInterface;

class Collection extends GraphQLQuery
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
     * @param string $siteHandle Site Handle of page content
     * @param int|null        $category
     * @param int|null        $tag
     * @param string|null     $before
     * @param string|null     $after
     * @param string|null     $search
     * @param int             $limit
     * @param int             $page
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        string $siteHandle,
        int $category = null,
        int $tag = null,
        string $before = null,
        string $after = null,
        string $search = null,
        int $limit = 10,
        int $page = 1,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/blog/collection.graphql')
            ->setRootPropertyPath('[entries]')
            ->setTotalResults('[total]')
            ->setResultsPerPage($limit)
            ->setCurrentPage($page)

            ->addVariable('site', $siteHandle)
            ->addVariable('category', $category)
            ->addVariable('tag', $tag)
            ->addVariable('before', $before)
            ->addVariable('after', $after)
            ->addVariable('search', $search)
            ->addVariable('limit', $limit)
            ->addVariable('offset', ($page - 1) * $limit)
            ->cache($cacheLifetime)
            ->cacheTags(['blogPosts'])
        ;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[id]' => '[id]',
            '[slug]' => '[slug]',
            '[url]' => new CallableData([$this, 'transformUrl'], '[slug]', '[year]'),
            '[title]' => '[title]',
            '[author]' => '[author]',
            '[authors]' => '[authors]',
            '[category]' => new CallableData([$this, 'transformCategory'], '[categories]'),
            '[tags]' => new MapArray('[tags]', [
                '[title]' => '[title]',
                '[url]'   => new CallableData([$this, 'transformTagUrl'], '[slug]'),
            ]),
            '[date]' => new DateTimeValue('[date]'),
            '[excerpt]' => '[excerpt]',
            '[thumbnailImage]' => new CallableData([$this, 'transformThumbnail'], '[thumbnailImage][0]'),
            '[thumbnailAltText]' => '[thumbnailAltText]'
        ];
    }

    public function transformThumbnail(?array $data): array
    {
        if (!$data) {
            return [];
        }

        return [
            'url'    => $data['url'],
            'srcset' => preg_replace('/ 580w/', ' 2x', $data['srcset'])
        ];
    }

    public function transformUrl(string $slug, string $year): string
    {
        return $this->router->generate('app_blog_show', ['slug' => $slug, 'year' => $year]);
    }

    public function transformCategory(array $data): array
    {
        if (count($data) > 0) {
            return [
                'url' => $this->router->generate('app_blog_category', ['slug' => $data[0]['slug']]),
                'title' => $data[0]['title']
            ];
        }

        return [];
    }

    public function transformTagUrl(string $slug): string
    {
        return $this->router->generate('app_blog_tag', ['slug' => $slug]);
    }
}
