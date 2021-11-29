<?php

declare(strict_types=1);

namespace App\Service;

use Strata\Frontend\Site;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
    private UrlGeneratorInterface $router;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    /**
     * Configure the Site
     * @param Site $site
     */
    public function configure(Site $site): void
    {
        // Setup locales
        // @todo replace hardcoded labels with translations
        $site->addDefaultLocale('en', [
            'siteId' => 1,
            'siteLink' =>  [
                'label' => 'English homepage',
                'url'   => $this->router->generate('app_default_home.en'),
            ]
        ]);
        $site->addLocale('ja', [
            'siteId' => 2,
            'siteLink' =>  [
                'label' => '日本語ホームページ',
                'url'   => $this->router->generate('app_default_home.ja'),
            ]
        ]);
        $site->addLocale('zh-hans', [
            'siteId' => 4,
            'siteLink' =>  [
                'label' => '简体中文首页',
                'url'   => $this->router->generate('app_default_home.zh-hans'),
            ]
        ]);
        $site->addLocale('pt-br', ['siteId' => 3]);
        $site->addLocale('hu', ['siteId' => 5]);
        $site->addLocale('fr', ['siteId' => 6]);
        $site->addLocale('es', ['siteId' => 7]);
        $site->addLocale('de', ['siteId' => 8]);
        $site->addLocaleRtl('ar', ['siteId' => 9]);

        // Set current locale
        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            $site->setLocale($request->getLocale());
        }
    }
}
