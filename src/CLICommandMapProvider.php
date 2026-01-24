<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Cekta\Migrator\Command\Migrate;
use Cekta\Migrator\Command\Rollback;
use Psr\Container\ContainerInterface;

class CLICommandMapProvider implements Lazy
{
    public function load(ContainerInterface $container): mixed
    {
        return [
            'migrate' => Migrate::class,
            'migration:rollback' => Rollback::class,
        ];
    }
}
