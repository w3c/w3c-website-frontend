<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Ecosystems;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Query\GraphQLQuery;

class Testimonials extends GraphQLQuery
{
    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    public function __construct(
        int $ecosystemId,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/ecosystems/testimonials.graphql')
            ->setRootPropertyPath('[entries]')

            ->addVariable('ecosystemId', $ecosystemId)
//            ->enableCache($cacheLifetime)
            //->setCacheTags($uri)
        ;
    }
}
