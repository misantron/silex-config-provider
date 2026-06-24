<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withParallel()
    ->withCache(directory: '.cache/ecs')
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPreparedSets(common: true)
    ->withPhpCsFixerSets(perCS20: true)
    ->withSkip([
        PhpdocLineSpanFixer::class,
    ])
;
