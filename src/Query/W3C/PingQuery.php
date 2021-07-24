<?php

declare(strict_types=1);

namespace App\Query\W3C;

use App\Service\W3C;
use Strata\Data\Query\Query;

/**
 * Send a healthcheck request to W3C API to check its online

 */
class PingQuery extends Query
{

    public function __construct()
    {
        $this->setUri('healthcheck')
            ->disableCache()
        ;
    }

    public function getRequiredDataProviderClass(): string
    {
        return W3C::class;
    }

    /**
     * Check all services are online
     *
     * @return bool
     * @throws \Strata\Data\Exception\MapperException
     */
    public function get(): bool
    {
        $data = parent::get();

        if ($data['app'] === true && $data['database'] === true) {
            return true;
        }
    }

}