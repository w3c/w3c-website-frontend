<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Newsletters;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\DateTimeValue;
use Strata\Data\Transform\Value\IntegerValue;
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
     * @param string          $siteHandle Site handle of page content
     * @param int|null        $year
     * @param int             $limit
     * @param int             $page
     *
     * @throws GraphQLQueryException
     */
    public function __construct(
        RouterInterface $router,
        string $siteHandle,
        int $year = null,
        int $limit = 10,
        int $page = 1
    ) {
        $this->router = $router;

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/newsletters/collection.graphql')
            ->setRootPropertyPath('[entries]')
            ->setTotalResults('[total]')
            ->setResultsPerPage($limit)
            ->setCurrentPage($page)
            ->cacheTags(['newsletter'])
            ->addVariable('site', $siteHandle)
            ->addVariable('limit', $limit)
            ->addVariable('offset', ($page - 1) * $limit)
        ;

        if ($year) {
            $this->addVariable('year', ['and', '>=' . $year, '<' . ($year+1)]);
        }
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[title]' => '[title]',
            '[url]'   => new CallableData([$this, 'transformUrl'], '[year]', '[month]', '[day]'),
            '[date]'  => new DateTimeValue('[date]'),
            '[year]'  => new IntegerValue('[year]'),
            '[month]' => new IntegerValue('[month]'),
            '[day]'   => new IntegerValue('[day]')
        ];
    }

    public function transformUrl(string $year, string $month, string $day): string
    {
        return $this->router->generate('app_newsletter_show', ['year' => $year, 'month' => $month, 'day' => $day]);
    }
}
