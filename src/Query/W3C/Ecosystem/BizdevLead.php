<?php

declare(strict_types=1);

namespace App\Query\W3C\Ecosystem;

use App\Service\W3C;
use Strata\Data\Mapper\MappingStrategyInterface;
use Strata\Data\Query\Query;

class BizdevLead extends Query
{
    public function __construct(string $hash)
    {
        $this->setUri('/users/' . $hash);
    }

    public function getRequiredDataProviderClass(): string
    {
        return W3C::class;
    }

    public function getMapping(): MappingStrategyInterface|array
    {
        return [
            '[name]'       => '[name]',
            '[work_title]' => '[work-title]',
            '[phone]'      => '[phone]',
            '[email]'      => '[email]',
        ];
    }
}
