<?php

declare(strict_types=1);

namespace App\Service;

use App\Query\CraftCMS\GlobalNavigation;
use App\Query\CraftCMS\SinglesBreadcrumbs;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Configure the QueryManager service
 *
 * Access the query manager in your controller by type hinting:
 *   QueryManager $manager
 *
 * @package App\QueryManager
 */
class QueryManagerConfigurator
{
    private W3C $w3CApi;
    private CraftCMS $craftCmsApi;
    private Site $site;
    private EventDispatcherInterface $eventDispatcher;
    private CacheItemPoolInterface $cache;
    private bool $enableCache;
    private HttpClientInterface $httpClient;

    public function __construct(
        W3C $w3cApi,
        CraftCMS $craftCmsApi,
        Site $site,
        EventDispatcherInterface $eventDispatcher,
        CacheItemPoolInterface $cache,
        ContainerBagInterface $params,
        HttpClientInterface $httpClient
    ) {
        $this->w3CApi = $w3cApi;
        $this->craftCmsApi = $craftCmsApi;
        $this->site = $site;
        $this->eventDispatcher = $eventDispatcher;
        $this->cache = $cache;
        $this->enableCache = (bool) $params->get('app.cacheEnable');
        $this->httpClient = $httpClient;
    }

    /**
     * Configure the QueryManager
     * @param QueryManager $manager
     */
    public function configure(QueryManager $manager): void
    {
        // Add data providers
        $manager->addDataProvider('craft', $this->craftCmsApi);
        $manager->addDataProvider('w3c', $this->w3CApi);

        // Use Sf's HTTP client
        $manager->setHttpClient($this->httpClient);
        
        // Event dispatcher
        $manager->getDataProvider('craft')->setEventDispatcher($this->eventDispatcher);

        // Set cache
        $manager->setCache($this->cache);

        if (!$this->enableCache) {
            $manager->disableCache();
        }

        // Please note queries added here are not affected by preview mode disabling the cache

        // Add global navigation
        $manager->add('navigation', new GlobalNavigation($this->site->siteId));

        // Add breadcrumbs for Craft singles
        $manager->add('singles-breadcrumbs', new SinglesBreadcrumbs($this->site->siteId));
    }
}
