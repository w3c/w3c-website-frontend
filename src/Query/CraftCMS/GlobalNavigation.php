<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\CacheException;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\Routing\RouterInterface;

/**
 * Get global navigation
 */
class GlobalNavigation extends GraphQLQuery
{
    private RouterInterface $router;

    /**
     * Set up query
     *
     * @param RouterInterface $router
     * @param string          $siteHandle    Site handle to generate global navigation for
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 24 hours
     * @param int             $limit         Results per page, defaults to 6
     *
     * @throws CacheException
     * @throws GraphQLQueryException
     * @throws QueryException
     */
    public function __construct(
        RouterInterface $router,
        string $siteHandle,
        int $cacheLifetime = CacheLifetime::DAY,
        int $limit = 6
    ) {
        $this->router = $router;
        $this->setGraphQLFromFile(__DIR__ . '/graphql/global-navigation.graphql')
            ->setRootPropertyPath('[entries]')
            ->setTotalResults('[total]')
            ->setResultsPerPage($limit)

            // Set site ID to retrieve navigation for
            ->addVariable('site', $siteHandle)

            // Limit results so we don't break the nav layout
            ->addVariable('limit', $limit)

            // Cache navigation response
            ->cache($cacheLifetime)
            ->cacheTagGlobal()
        ;
    }

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Populate title_link value
     *
     * titleLink
     * if "isTitleLinkInternal": true, then url = '/' + titleInternalLink.uri
     * if false, url =  titleExternalLink
     *
     * @param bool        $isTitleLinkInternal
     * @param array|null  $titleInternalLink
     * @param string|null $titleExternalLink
     *
     * @return string|null
     */
    public function transformTitleLink(
        bool $isTitleLinkInternal,
        ?array $titleInternalLink,
        ?string $titleExternalLink
    ): ?string {
        if (!$isTitleLinkInternal && $titleExternalLink) {
            return $titleExternalLink;
        }

        if ($titleInternalLink && array_key_exists('sectionHandle', $titleInternalLink)) {
            switch ($titleInternalLink['sectionHandle']) {
                case 'ecosystems':
                    return $this->router->generate('app_ecosystem_show', ['slug' => $titleInternalLink['slug']]);
                default:
                    return $this->router->generate('app_default_index', ['route' => $titleInternalLink['uri']]);
            }
        }

        return null;
    }

    /**
     * Populate children links
     *
     * children
     * if isset internalLink.uri, url = '/' + internalLink.uri
     *
     * @param string|null $url
     * @param array|null  $internalUri
     *
     * @return string|null
     */
    public function transformChildLink(?string $url, ?array $internalUri): ?string
    {
        if ($url) {
            return $url;
        }

        switch ($internalUri['sectionHandle']) {
            case 'ecosystems':
                return $this->router->generate('app_ecosystem_show', ['slug' => $internalUri['slug']]);
            default:
                return $this->router->generate('app_default_index', ['route' => $internalUri['uri']]);
        }
    }

    /**
     * Return mapping strategy to use to map a single item
     *
     * @return array
     */
    public function getMapping(): array
    {
        return [
            '[title]'      => '[title]',
            '[titleLink]' => new CallableData(
                [$this, 'transformTitleLink'],
                '[isTitleLinkInternal]',
                '[titleInternalLink][0]',
                '[titleExternalLink]'
            ),
            '[introText]'  => '[introText]',
            '[introLinks]' => '[introLinks]',
            '[children]'   => new MapArray('[children]', [
                '[title]' => '[title]',
                '[url]' => new CallableData([$this, 'transformChildLink'], '[url]', '[internalLink][0]'),
            ]),
        ];
    }
}
