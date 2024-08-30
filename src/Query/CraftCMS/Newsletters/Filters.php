<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Newsletters;

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

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param string $siteHandle        Site ID of page content
     * @param int $cacheLifetime Cache lifetime to store HTTP response for, defaults to 1 hour
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
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/newsletters/filters.graphql')
            ->addVariable('site', $siteHandle)
            ->cache($cacheLifetime)
        ;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[archives]' => new CallableData([$this, 'transformArchives'], '[first][year]', '[last][year]')
        ];
    }

    public function transformArchives(string $first = null, string $last = null): array
    {
        if (!$first) {
            return [];
        }

        $archives = [];
        for ($year = $last; $year >= $first; $year--) {
            $archives[] = [
                'title' => $year,
                'url'   => $this->router->generate('app_newsletter_archive', ['year' => $year])
            ];
        }

        return $archives;
    }
}
