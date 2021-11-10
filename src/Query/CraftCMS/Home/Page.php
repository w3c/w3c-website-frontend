<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Home;

use App\Service\CraftCMS;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;

class Page extends GraphQLQuery
{
    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param int $siteId        Site ID of page content
     *
     * @throws GraphQLQueryException
     */
    public function __construct(int $siteId)
    {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/home/page.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/contentImage.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/seoData.graphql')
             ->setRootPropertyPath('[entry]')

            // Set site ID to retrieve navigation for
             ->addVariable('siteId', $siteId)

            // Caching
             ->doNotCache();
    }

    public function getMapping()
    {
        $mapping = new WildcardMappingStrategy();
        $mapping->addMapping('heroIllustration', ['[heroIllustration]' => '[heroIllustration][0]']);
        $mapping->addMapping('heroCallToActionButton', ['[heroCallToActionButton]' => '[heroCallToActionButton][0]']);
        $mapping->addMapping(
            'workingWithIndustryCallToActionButton',
            ['[workingWithIndustryCallToActionButton]' => '[workingWithIndustryCallToActionButton][0]']
        );
        $mapping->addMapping('heroIllustration', ['[heroIllustration]' => '[heroIllustration][0]']);

        return $mapping;
    }
}
