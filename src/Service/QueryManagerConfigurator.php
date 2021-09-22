<?php

declare(strict_types=1);

namespace App\Service;

use App\Query\CraftCMS\GlobalNavigation;
use App\Query\W3C\Healthcheck;
use Strata\Data\Query\QueryManager;
use Strata\Frontend\Site;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\HttpFoundation\RequestStack;

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

    public function __construct(W3C $w3cApi, CraftCMS $craftCmsApi, Site $site)
    {
        $this->w3CApi = $w3cApi;
        $this->craftCmsApi = $craftCmsApi;
        $this->site = $site;
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

        // Set cache, but disable it initially.
        // To use, use $manager->enableCache($lifetime) for all queries or $query->enableCache($lifetime) for individual queries
        $cache = new FilesystemTagAwareAdapter('cache', 0, __DIR__ . '/../../var/cache/');
        $manager->setCache($cache);
        $manager->disableCache();

        // Add healthcheck queries
        $manager->add('w3c_healthcheck', new Healthcheck());

        // Add global navigation
        $manager->add('navigation', new GlobalNavigation($this->site->siteId));
    }
}
