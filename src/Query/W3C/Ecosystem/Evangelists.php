<?php

declare(strict_types=1);

namespace App\Query\W3C\Ecosystem;

use App\Service\W3C;
use Strata\Data\Query\Query;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\WildcardMappingStrategy;

class Evangelists extends Query
{
    public function __construct(string $slug, int $page = 1, int $perPage = 500)
    {
        $this->setUri('/ecosystems/' . $slug . '/evangelists')
            ->addParam('items', $perPage)
            ->addParam('embed', true)
            ->addParam('page', $page)
            ->setRootPropertyPath('[_embedded][evangelists]')
            ->setCurrentPage('[page]')
            ->setTotalResults('[total]')
            ->setResultsPerPage('[limit]')
            ->disableCache();
        ;
    }

    public function getRequiredDataProviderClass(): string
    {
        return W3C::class;
    }

    public function getMapping(): array
    {
        return [
            '[name]'        => '[name]',
            '[work_title]'        => '[work-title]',
            '[phone]' => '[phone]',
            '[email]'         => '[email]',
        ];
    }
}
