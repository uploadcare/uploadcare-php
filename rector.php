<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules(
        [Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector::class],
    )
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
        __DIR__ . '/api-samples',
    ])
    ->withCache(
        'rector-cache',
        Rector\Caching\ValueObject\Storage\FileCacheStorage::class,
    )
    ->withParallel();
