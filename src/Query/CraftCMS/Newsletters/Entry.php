<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Newsletters;

use App\Service\CraftCMS;
use DateInterval;
use DateTimeImmutable;
use Exception;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Query\GraphQLQuery;

class Entry extends GraphQLQuery
{
    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * @throws GraphQLQueryException
     * @throws Exception
     */
    public function __construct(
        string $siteHandle,
        int $year,
        int $month,
        int $day
    ) {
        $date = new DateTimeImmutable($year . '-' . $month . '-' . $day);
        $nextDay = $date->add(new DateInterval('P1D'));

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/newsletters/entry.graphql')
            ->setRootPropertyPath('[entry]')
            ->addVariable('site', $siteHandle)
            ->addVariable('date', ['and', '>=' . $date->format('Y-m-d'), '<' . $nextDay->format('Y-m-d')])
            ->cacheTags(['newsletter'])
        ;
    }
}
