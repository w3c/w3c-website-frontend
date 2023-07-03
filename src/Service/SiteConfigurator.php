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
            'siteHandle'   => 'default',
            'siteLink' =>  [
                'label' => 'English homepage',
                'url'   => $this->router->generate('app_default_home.en', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]
        ]);
        $site->addLocale('ja', [
            'siteHandle' => 'w3c_japan',
            'siteLink' =>  [
                'label' => '日本語ホームページ',
                'url'   => $this->router->generate('app_default_home.ja', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            'searchPattern' => '*%s*'
        ]);
        $site->addLocale('zh-hans', [
            'siteHandle' => 'w3c_china',
            'siteLink' =>  [
                'label' => '简体中文首页',
                'url'   => $this->router->generate('app_default_home.zh-hans', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            'searchPattern' => '*%s*',
        ]);
        $site->addLocale('af', ['siteHandle' => 'afrikaans']);
        $site->addLocaleRtl('ar', ['siteHandle' => 'arabic']);
        $site->addLocale('bg', ['siteHandle' => 'bulgarian']);
        $site->addLocale('cs', ['siteHandle' => 'czech']);
        $site->addLocale('da', ['siteHandle' => 'danish']);
        $site->addLocale('de', ['siteHandle' => 'german']);
        $site->addLocale('el', ['siteHandle' => 'greek']);
        $site->addLocale('es', ['siteHandle' => 'spanish']);
        $site->addLocale('et', ['siteHandle' => 'estonian']);
        $site->addLocale('fi', ['siteHandle' => 'finnish']);
        $site->addLocale('fr', ['siteHandle' => 'french']);
        $site->addLocale('ga', ['siteHandle' => 'gaelic']);
        $site->addLocale('hi', ['siteHandle' => 'hindi']);
        $site->addLocale('hu', ['siteHandle' => 'hungarian']);
        $site->addLocale('it', ['siteHandle' => 'italian']);
        $site->addLocale('ko', ['siteHandle' => 'korean']);
        $site->addLocale('lt', ['siteHandle' => 'lithuanian']);
        $site->addLocale('lv', ['siteHandle' => 'latvian']);
        $site->addLocale('mt', ['siteHandle' => 'maltese']);
        $site->addLocale('nl', ['siteHandle' => 'dutch']);
        $site->addLocale('pt-br', ['siteHandle' => 'brazilianPortuguese']);
        $site->addLocale('pt', ['siteHandle' => 'portuguese']);
        $site->addLocale('ro', ['siteHandle' => 'romanian']);
        $site->addLocale('ru', ['siteHandle' => 'russian']);
        $site->addLocale('sk', ['siteHandle' => 'slovak']);
        $site->addLocale('sl', ['siteHandle' => 'slovenian']);
        $site->addLocale('sv', ['siteHandle' => 'swedish']);
        $site->addLocale('zu', ['siteHandle' => 'zulu']);

        // Set current locale
        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            $site->setLocale($request->getLocale());
        }
    }
}
