<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Class GraphQlDataFormatter
 * @package App\Service
 */
class GraphQlDataFormatter
{
    /**
     * @param ResponseInterface $data
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public static function formatLocalisationDataForView(ResponseInterface $data): array
    {
        $data = $data->toArray();
        $localisations = $data['data']['entry']['localized'];

        $response = [];

        foreach ($localisations as $localisation) {
            $item = [];
            $item['title'] = $localisation['title'];
            $item['code'] = $localisation['code'];
            $item['dir'] = 'ltr';

            $response[$localisation['url']] = $item;
        }

        return $response;
    }

    /**
     * @param ResponseInterface $data
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public static function formatLandingPageContentDataForView(ResponseInterface $data): array
    {
        $data = $data->toArray();
        $pagecontent = $data['data']['entry']['landingFlexibleComponents'];

        return $pagecontent;
    }
}