<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withParallel()
    ->withCache(cacheDirectory: '.cache/rector')
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/tests/resources',
    ])
    ->withImportNames(importShortClasses: false)
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        earlyReturn: true,
        phpunitCodeQuality: true
    )
;
