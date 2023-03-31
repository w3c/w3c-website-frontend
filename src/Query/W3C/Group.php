<?php

declare(strict_types=1);

namespace App\Query\W3C;

use App\Service\W3C;
use Strata\Data\Query\Query;

class Group extends Query
{
    public function __construct(string $type, string $shortname)
    {
        $this->setUri('/groups/' . $type . '/' . $shortname)
             ->addParam('embed', true)
        ;
    }

    public function getRequiredDataProviderClass(): string
    {
        return W3C::class;
    }
}
