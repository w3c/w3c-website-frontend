<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Feeds;

use App\Service\CraftCMS;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;

class Taxonomy extends GraphQLQuery
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    public function __construct(
        string $siteHandle,
        int $limit,
        int $category = null,
        int $ecosystem = null,
        int $group = null
    ) {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/feeds/taxonomy.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/defaultFlexibleComponents.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/thumbnailImage.graphql')
             ->setRootPropertyPath('[entries]')
             ->addVariable('site', $siteHandle)
             ->addVariable('limit', $limit)
             ->addVariable('category', $category)
             ->addVariable('ecosystem', $ecosystem)
             ->addVariable('group', $group);
    }

    public function getMapping()
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('page', ['[page]' => '[page][0]']);

        return $mapping;
    }
}
