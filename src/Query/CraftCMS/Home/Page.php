<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Home;

use App\Service\CraftCMS;
use Strata\Data\Exception\GraphQLQueryException;
use Strata\Data\Mapper\MapArray;
use Strata\Data\Mapper\WildcardMappingStrategy;
use Strata\Data\Query\GraphQLQuery;
use Strata\Data\Transform\Data\CallableData;
use Symfony\Component\Routing\RouterInterface;

class Page extends GraphQLQuery
{
    private RouterInterface $router;

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    /**
     * Set up query
     *
     * @param RouterInterface $router
     * @param string $siteHandle Site ID of page content
     *
     * @throws GraphQLQueryException
     */
    public function __construct(RouterInterface $router, string $siteHandle)
    {
        $this->router = $router;
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/home/page.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/thumbnailImage.graphql')
             ->addFragmentFromFile(__DIR__ . '/../graphql/fragments/contentImage.graphql')
             ->setRootPropertyPath('[entry]')

            // Set site ID to retrieve navigation for
             ->addVariable('site', $siteHandle)

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
        $mapping->addMapping('latestNewsFeaturedArticles', [
            '[latestNewsFeaturedArticles]' => new MapArray('[latestNewsFeaturedArticles]', [
                '[category]'         => '[sectionHandle]',
                '[title]'            => '[title]',
                '[text]'             => '[language_code]',
                '[img]'              => '[thumbnailImage][0]',
                '[thumbnailAltText]' => '[thumbnailAltText]',
                '[url]'              => new CallableData(
                    [$this, 'transformEntryUri'],
                    '[sectionHandle]',
                    '[slug]',
                    '[year]'
                )
            ])
        ]);

        return $mapping;
    }

    public function transformEntryUri(string $sectionHandle, string $slug, string $year): ?string
    {
        switch ($sectionHandle) {
            case 'blogPosts':
                $route = 'app_blog_show';
                break;
            case 'pressReleases':
                $route = 'app_pressreleases_show';
                break;
            case 'newsArticles':
                $route = 'app_news_show';
                break;
            default:
                return null;
        }

        return $this->router->generate($route, ['slug' => $slug, 'year' => $year]);
    }
}
