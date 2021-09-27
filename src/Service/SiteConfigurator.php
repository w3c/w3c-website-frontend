<?php

declare(strict_types=1);

namespace App\Service;

use Strata\Frontend\Site;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Configure the Site object
 *
 * Access the query manager in your controller by type hinting:
 *   Site $site
 *
 * @package App\QueryManager
 */
class SiteConfigurator
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Configure the Site
     * @param Site $site
     */
    public function configure(Site $site): void
    {
        // Setup locales
        $site->addLocale('en', ['siteId' => 1]);
        $site->addLocale('ja', ['siteId' => 2]);
        $site->addLocale('pt-br', ['siteId' => 3]);
        $site->addLocale('zh-hans', ['siteId' => 4]);
        $site->addLocale('hu', ['siteId' => 5]);
        $site->addLocale('fr', ['siteId' => 6]);
        $site->addLocale('es', ['siteId' => 7]);
        $site->addLocale('de', ['siteId' => 8]);
        $site->addLocaleRtl('ar', ['siteId' => 9]);

        // Set current locale
        $site->setLocale($this->requestStack->getCurrentRequest()->getLocale());
    }
}
