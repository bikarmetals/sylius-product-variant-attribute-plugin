<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->paths([
        __DIR__ . '/src'
    ]);
    $config->import(__DIR__ . '/vendor/sylius-labs/coding-standard/ecs.php');
};
