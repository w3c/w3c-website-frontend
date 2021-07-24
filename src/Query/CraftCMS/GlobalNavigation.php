<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;

/**
 * Get global navigation
 */
class GlobalNavigation extends GraphQLQuery
{

    /**
     * Set up query
     *
     * @param int $siteId Site ID to generate global navigation for
     * @param int $cacheLifetime Cache lifetime to store HTTP response for, defaults to 24 hours
     * @param int $limit Results per page, defaults to 6
     * @throws \Strata\Data\Exception\GraphQLQueryException
     */
    public function __construct(int $siteId, int $cacheLifetime = CacheLifetime::DAY, int $limit = 6)
    {
        $this->setGraphQLFromFile(__DIR__ . '/graphql/global-navigation.graphql')
            ->setRootPropertyPath('[entries]')
            ->setTotalResults('[total]')
            ->setResultsPerPage($limit)

            // Set site ID to retrieve navigation for
            ->addVariable('siteId', $siteId)

            // Limit results so we don't break the nav layout
            ->addVariable('limit', $limit)

            // Cache navigation response
            ->enableCache($cacheLifetime)
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
     * @param array $data
     * @return string|null
     */
    public function transformTitleLink(bool $isTitleLinkInternal, ?string $titleInternalLink, ?string $titleExternalLink): ?string
    {
        if ($isTitleLinkInternal && !empty($titleInternalLink)) {
            return '/' . $titleInternalLink;
        }
        if (!$isTitleLinkInternal && !empty($titleExternalLink)) {
            return $titleExternalLink;
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
     * @param string|null $internalUri
     * @return string|null
     */
    public function transformChildLink(?string $url, ?string $internalUri): ?string
    {
        if (!empty($internalUri)) {
            return '/' . $internalUri;
        }
        if (!empty($url)) {
            return $url;
        }
        return null;
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
            '[titleLink]'  => new CallableData([$this, 'transformTitleLink'], '[isTitleLinkInternal]', '[titleInternalLink][0][uri]', '[titleExternalLink]'),
            '[introText]'  => '[introText]',
            '[introLinks]' => '[introLinks]',
            '[children]'   => new MapArray('[children]', [
                '[title]' => '[title]',
                '[url]'   => new CallableData([$this, 'transformChildLink'], '[url]', '[internalLink][0][uri]'),
            ]),
        ];
    }

}