<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\PressReleases;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
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
     * @param int             $siteId        Site ID of page content
     * @param string|null     $before
     * @param string|null     $after
     * @param int             $limit
     * @param int             $page
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        int $siteId,
        string $before = null,
        string $after = null,
        int $limit = 10,
        int $page = 1,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {
        $this->router = $router;

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/press-releases/collection.graphql')
            ->setRootPropertyPath('[entries]')
            ->setTotalResults('[total]')
            ->setResultsPerPage($limit)
            ->setCurrentPage($page)

            ->addVariable('siteId', $siteId)
            ->addVariable('before', $before)
            ->addVariable('after', $after)
            ->addVariable('limit', $limit)
            ->addVariable('offset', ($page - 1) * $limit)
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping()
    {
        return [
            '[id]'               => '[id]',
            '[slug]'             => '[slug]',
            '[url]'              => new CallableData([$this, 'transformUrl'], '[slug]', '[year]'),
            '[title]'            => '[title]',
            '[date]'             => new DateTimeValue('[date]'),
            '[year]'             => '[year]',
            '[excerpt]'          => '[excerpt]',
            '[thumbnailImage]'   => new CallableData([$this, 'transformThumbnail'], '[thumbnailImage][0]'),
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
        return $this->router->generate('app_pressreleases_show', ['slug' => $slug, 'year' => $year]);
    }
}
