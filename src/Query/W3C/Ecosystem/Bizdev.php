<?php

declare(strict_types=1);

namespace App\Query\W3C\Ecosystem;

use App\Service\W3C;
use Strata\Data\Query\Query;

class Bizdev extends Query
{
    public function __construct()
    {
        $this->setUri('/functions/bizdev');
    }

    public function getRequiredDataProviderClass(): string
    {
        return W3C::class;
    }
}
