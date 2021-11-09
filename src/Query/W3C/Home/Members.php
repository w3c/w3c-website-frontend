<?php

declare(strict_types=1);

namespace App\Query\W3C\Home;

use App\Service\W3C;
use Strata\Data\Query\Query;
use Strata\Data\Transform\Data\CallableData;

class Members extends Query
{
    public function __construct(int $page = 1, int $perPage = 500)
    {
        $this->setUri('/affiliations')
            ->addParam('is-member', true)
            ->addParam('with-logo', true)
            ->addParam('items', $perPage)
            ->addParam('embed', true)
            ->addParam('page', $page)
            ->setRootPropertyPath('[_embedded][affiliations]')
            ->setCurrentPage('[page]')
            ->setTotalResults('[total]')
            ->setResultsPerPage('[limit]')
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
            '[logo]'      => '[_links][logo][href]'
        ];
    }
}
