<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Bridge\Symfony\Routing\SymfonyRoutesProvider;
use Rector\Symfony\Contract\Bridge\Symfony\Routing\SymfonyRoutesProviderInterface;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/templates',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withTypeCoverageLevel(0)
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
    ->withSets(
        [
            SymfonySetList::SYMFONY_71,
            SymfonySetList::SYMFONY_CODE_QUALITY,
            SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
            SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
            SymfonySetList::CONFIGS,
        ]
    )
    ->withSymfonyContainerPhp(__DIR__ . '/tests/symfony-container.php')
    ->registerService(SymfonyRoutesProvider::class, SymfonyRoutesProviderInterface::class)
;
