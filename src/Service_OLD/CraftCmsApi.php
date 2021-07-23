<?php

declare(strict_types=1);

namespace App\Service;

use Strata\Frontend\Repository\CraftCms\CraftCms;

class CraftCmsApi extends CraftCms
{
    /**
     * Constructor
     *
     * Auto-populates data provider from parameters stored in config/services.yaml
     *
     */

    /**
     * CraftCmsApi constructor.
     *
     * Auto-populates constructor arguments from service definition
     * @see config/services.yaml
     * @param string $apiUrl
     * @param string $apiKey
     */
    public function __construct(string $apiUrl, string $apiKey)
    {
        parent::__construct($apiUrl);

        // Set default auth token
        $this->setAuthorization($apiKey);
    }

}