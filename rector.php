<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/Config',
        __DIR__ . '/Migrations',
        __DIR__ . '/Reports',
        __DIR__ . '/Source',
        __DIR__ . '/Tests',
    ]);

    $rectorConfig->importNames(importDocBlockNames: false);
    $rectorConfig->sets([SetList::PHP_82]);
};
