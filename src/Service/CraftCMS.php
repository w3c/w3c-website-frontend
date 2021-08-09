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

    public static function getSiteForLocale(string $locale): int
    {
        $lang = strtolower(explode('_', $locale)[0]);
        switch ($lang) {
            case 'ja':
                return 2;
            case 'zh-hans':
                return 3;
            default:
                return 1;
        }
    }
}
