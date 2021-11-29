<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Ecosystems;

use App\Service\CraftCMS;
use Strata\Data\Cache\CacheLifetime;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Strata\Frontend\Site;

class Testimonials extends GraphQLQuery
{
    private Site $site;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    public function __construct(
        int $ecosystemId,
        Site $site,
        int $cacheLifetime = CacheLifetime::HOUR
    ) {

        $this->setGraphQLFromFile(__DIR__ . '/../graphql/ecosystems/testimonials.graphql')
            ->setRootPropertyPath('[entries]')

            ->addVariable('ecosystemId', $ecosystemId)
//            ->enableCache($cacheLifetime)
            //->setCacheTags($uri)
        ;

        $this->site = $site;
    }

    public function getMapping()
    {

        return [
            '[quote]' => '[quote]',
            '[author]' => '[author]',
            '[authorJobTitle]' => '[authorJobTitle]',
            '[organization]' => '[organization]',
            '[logo]' => '[logo][0][url]',
            '[language]' => '[language]',
            '[text_direction]' => new CallableData([ $this, 'getLanguageDirection' ], '[language]'),
        ];
    }

    public function getLanguageDirection(string $language_code)
    {
        return $this->site->getTextDirection($language_code);
    }
}
