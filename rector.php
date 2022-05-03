<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;

return static function (RectorConfig $config): void {
    $config->paths([
        __DIR__.'/dev',
        __DIR__.'/lib',
        __DIR__.'/src',
        __DIR__.'/tests',
        __DIR__.'/utils'
    ]);
    $config->phpVersion(PhpVersion::PHP_81);
    $config->phpstanConfig(__DIR__.'/phpstan.neon.dist');
};
