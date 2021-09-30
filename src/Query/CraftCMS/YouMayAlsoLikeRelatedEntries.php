<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;

/**
 * Get global navigation
 */
class YouMayAlsoLikeRelatedEntries extends GraphQLQuery
{

    /**
     * Set up query
     *
     * @param int    $siteId        Site ID to generate global navigation for
     * @param string $uri
     * @param int    $cacheLifetime Cache lifetime to store HTTP response for, defaults to 24 hours
     *
     * @throws GraphQLQueryException
     */
    public function __construct(int $siteId, string $uri, int $cacheLifetime = CacheLifetime::HOUR)
    {
        parent::__construct(__DIR__ . '/graphql/youMayAlsoLikeRelatedEntries.graphql');
        $this
            ->addFragmentFromFile(__DIR__. '/graphql/fragments/thumbnailImage.graphql')
            ->setRootPropertyPath('[entry]')

            // Set page URI to retrieve navigation for
            ->addVariable('uri', $uri)

            // Set site ID to retrieve navigation for
            ->addVariable('siteId', $siteId)

            // Cache page response
            ->enableCache($cacheLifetime)
            //->setCacheTags(['global'])
        ;
    }

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    public function transformImage(?array $entry)
    {
        if (array_key_exists('contentEntry', $entry)) {
            $entry = $entry['contentEntry'][0];
        }

        if (array_key_exists('thumbnailImage', $entry) && count($entry['thumbnailImage']) > 0) {
            if (array_key_exists('thumbnailAltText', $entry)) {
                return array_merge($entry['thumbnailImage'][0], ['alt' => $entry['thumbnailAltText']]);
            }

            return $entry['thumbnailImage'][0];
        }

        return null;
    }

    public function getMapping()
    {
        return [
            '[title]' => '[youMayAlsoLikeTitle]',
            '[text]'  => '[youMayAlsoLikeSectionIntroduction]',
            '[links]' => new MapArray(
                '[youMayAlsoLikeRelatedEntries]',
                [
                    '[title]'    => ['[title]', '[contentEntry][0][title]'],
                    '[url]'      => ['[url]', '[contentEntry][0][url]'],
                    '[category]' => ['[category]', '[contentEntry][0][category]'],
                    '[text]'     => ['[text]', '[contentEntry][0][text]'],
                    '[img]'      => new CallableData([$this, 'transformImage'])
                ]
            )
        ];
    }
}
