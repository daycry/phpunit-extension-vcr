<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSets([
        SetList::PHP_82,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
    ])
    ->withImportNames();
