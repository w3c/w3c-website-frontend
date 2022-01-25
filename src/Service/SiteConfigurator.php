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
        $site->addLocale('ru', ['siteId' => 10]);
        $site->addLocale('it', ['siteId' => 11]);
        $site->addLocale('sv', ['siteId' => 12]);
        $site->addLocale('ko', ['siteId' => 13]);
        $site->addLocale('el', ['siteId' => 14]);
        $site->addLocale('bg', ['siteId' => 15]);
        $site->addLocale('cs', ['siteId' => 16]);
        $site->addLocale('da', ['siteId' => 17]);
        $site->addLocale('et', ['siteId' => 18]);
        $site->addLocale('fi', ['siteId' => 19]);
        $site->addLocale('ga', ['siteId' => 20]);
        $site->addLocale('lt', ['siteId' => 21]);
        $site->addLocale('lv', ['siteId' => 22]);
        $site->addLocale('mt', ['siteId' => 23]);
        $site->addLocale('nl', ['siteId' => 24]);
        $site->addLocale('pt', ['siteId' => 25]);
        $site->addLocale('ro', ['siteId' => 26]);
        $site->addLocale('sk', ['siteId' => 27]);
        $site->addLocale('sl', ['siteId' => 28]);

        // Set current locale
        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            $site->setLocale($request->getLocale());
        }
    }
}
