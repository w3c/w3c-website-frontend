<?php

declare(strict_types=1);

namespace App\Service;

use Strata\Data\Http\Rest;

class W3C extends Rest
{

    /**
     * W3C constructor.
     *
     * Auto-populates constructor arguments from service definition
     * @see config/services.yaml
     * @param string $apiUrl
     * @param string $apiKey
     */
    public function __construct(string $apiUrl, string $apiKey)
    {
        parent::__construct($apiUrl);

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
            'query' => [
                'apikey' => $token
            ]
        ]);
    }
}
