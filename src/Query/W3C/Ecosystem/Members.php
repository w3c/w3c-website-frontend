<?php

declare(strict_types=1);

namespace App\Query\W3C\Ecosystem;

use App\Service\W3C;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Query\Query;
use Strata\Data\Transform\Data\CallableData;

class Members extends Query
{
    public function __construct(string $slug, int $page = 1, int $perPage = 500)
    {
        $this->setUri('/ecosystems/' . $slug . '/member-organizations')
            ->addParam('items', $perPage)
            ->addParam('embed', true)
            ->addParam('page', $page)
            ->setRootPropertyPath('[_embedded][affiliations]')
            ->setCurrentPage('[page]')
            ->setTotalResults('[total]')
            ->setResultsPerPage('[limit]')
//            ->disableCache()
        ;
    }

    public function getRequiredDataProviderClass(): string
    {
        return W3C::class;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[name]'        => '[name]',
            '[logo]'      => '[_links][logo][href]'
        ];
    }
}
