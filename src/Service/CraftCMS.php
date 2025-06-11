<?php

declare(strict_types=1);

namespace App\Service;

use Strata\Data\Http\GraphQL;

class CraftCMS extends GraphQL
{
    /**
     * CraftCmsApi constructor.
     *
     * Auto-populates constructor arguments from service definition
     *
     * @param string $apiUrl
     * @param string $apiKey
     * @see config/services.yaml
     */
    public function __construct(string $apiUrl, string $apiKey)
    {
        parent::__construct($apiUrl);

        // Set default auth token
        $this->setAuthorization($apiKey);
    }

    /**
     * Set API authorization token to use with all requests
     *
     * @param string $token
     */
    public function setAuthorization(string $token)
    {
        $this->setDefaultOptions([
            'auth_bearer' => $token
        ]);
    }
}
