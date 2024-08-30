<?php

declare(strict_types=1);

namespace App\Query\W3C\Ecosystem;

use App\Service\W3C;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Query\Query;

class Groups extends Query
{
    public function __construct(string $slug, int $page = 1, int $perPage = 500)
    {
        $this->setUri('/ecosystems/' . $slug . '/groups')
             ->addParam('items', $perPage)
             ->addParam('embed', true)
             ->addParam('page', $page)
             ->setRootPropertyPath('[_embedded][groups]')
             ->setCurrentPage('[page]')
             ->setTotalResults('[total]')
             ->setResultsPerPage('[limit]');
//             ->disableCache();
    }

    public function getRequiredDataProviderClass(): string
    {
        return W3C::class;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[name]'        => '[name]',
            '[type]'        => '[type]',
            '[description]' => '[description]',
            '[url]'         => '[_links][homepage][href]',
        ];
    }
}
