<?php

declare(strict_types=1);

namespace App\Query\W3C;

use App\Service\W3C;
use Strata\Data\Query\Query;

class W3CFunction extends Query
{
    public function __construct(string $type, string $shortname)
    {
        $this->setUri('/functions/' . $shortname)
             ->addParam('embed', true)
        ;
    }

    public function getRequiredDataProviderClass(): string
    {
        return W3C::class;
    }
}
