<?php

declare(strict_types=1);

namespace App\Query\CraftCMS;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\CacheException;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Exception\QueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Get global navigation
 */
class Globals extends GraphQLQuery
{
    private RouterInterface $router;
    private UrlHelper $urlHelper;

    /**
     * Set up query
     *
     * @param RouterInterface $router
     * @param UrlHelper       $urlHelper
     * @param string          $siteHandle    Site handle to generate global navigation for
     * @param int             $cacheLifetime Cache lifetime to store HTTP response for, defaults to 24 hours
     * @param int             $limit         Results per page, defaults to 6
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        UrlHelper $urlHelper,
        string $siteHandle
    ) {
        $this->router = $router;
        $this->urlHelper = $urlHelper;
        $this->setGraphQLFromFile(__DIR__ . '/graphql/globals.graphql')
            ->setRootPropertyPath('[globalSet]')

            // Set site ID to retrieve navigation for
            ->addVariable('site', $siteHandle)
        ;
    }

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Return mapping strategy to use to map a single item
     *
     * @return array
     */

    // @TODO: Currently returns null
    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[socialMedia]' => '[socialMedia]'
        ];
    }
}
