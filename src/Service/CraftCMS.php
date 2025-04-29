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
     * @param ?string $cookieValue w3csess auth cookie
     * @see config/services.yaml
     */
    public function __construct(string $apiUrl, string $apiKey, ?string $cookieValue = null)
    {
        parent::__construct($apiUrl);

        // Set default auth token
        $this->setAuthorization($apiKey, $cookieValue);
    }

    /**
     * Set API authorization token to use with all requests
     *
     * @param string $token
     * @param ?string $cookieValue w3csess auth cookie
     */
    public function setAuthorization(string $token, ?string $cookieValue = null)
    {
        $options = [
            'auth_bearer' => $token,
        ];
        if (null !== $cookieValue) {
            $options['headers'] = [
                'Cookie' => sprintf('w3csess=%s', $cookieValue)
            ];
        }
        $this->setDefaultOptions($options);
    }
}
