<?php

declare(strict_types=1);

namespace App\Service;

use Strata\Data\Http\Http;
use Strata\Data\Http\Response\CacheableResponse;
use Strata\Data\Http\Rest;
use Strata\Frontend\Repository\ContentRepository;
use Strata\Frontend\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class W3CApi extends ContentRepository implements RepositoryInterface
{
    /**
     * Constructor
     *
     * Auto-populates parameters from config/services.yaml
     *
     * @param ParameterBagInterface $parameters
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $baseUrl = $parameters->get('app.w3c_api_url');
        $this->provider = new Rest($baseUrl);

        $token = $parameters->get('app.w3c_api_token');
        $this->setAuthorization($token);
    }

    /**
     * Set API authorization token to use with all requests
     *
     * @param string $token
     */
    public function setAuthorization(string $token)
    {
        $this->getProvider()->setDefaultOptions([
            'query' => [
                'apikey' => $token
            ]
        ]);
    }

    /**
     * Return REST API data provider for W3C API
     * @return Rest
     */
    public function getProvider(): Rest
    {
        return $this->provider;
    }

    /**
     * Check the API is available
     *
     * @return bool
     * @throws \Strata\Data\Exception\DecoderException
     * @throws \Strata\Data\Exception\HttpException
     * @throws \Strata\Data\Exception\HttpNotFoundException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function ping(): bool
    {
        $response = $this->getProvider()->get('healthcheck');
        $data = $this->getProvider()->decode($response);
        if ($data['app'] === true && $data['database'] === true) {
            return true;
        }
        return false;
    }

    /**
     * Return specifications from API
     *
     * @param int $page
     * @param int|null $perPage
     * @param bool|null $embed
     * @return CacheableResponse
     * @throws \Strata\Data\Exception\HttpException
     * @throws \Strata\Data\Exception\HttpNotFoundException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getSpecifications(int $page = 1, ?int $perPage = null, ?bool $embed = null): CacheableResponse
    {
        $query = ['page' => $page];
        if ($perPage !== null) {
            $query['items'] = $perPage;
        }
        if ($embed !== null) {
            $query['embed'] = $embed;
        }
        return $this->getProvider()->get('specifications', $query);
    }

}