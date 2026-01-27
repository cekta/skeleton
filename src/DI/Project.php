<?php

declare(strict_types=1);

namespace App\DI;

use Cekta\DI\ClassLoader\Composer;
use Cekta\Migrator\Command\Migrate;
use Cekta\Migrator\Command\Rollback;

class Project extends \Cekta\DI\Project
{
    /**
     * @param array<string, string> $env
     */
    public function __construct(private readonly array $env = [])
    {
        parent::__construct(
            [
                new Module($this->env),
                new \Cekta\Framework\HTTP\Module(),
                new \Cekta\Framework\CLI\Module(
                    command_map: [
                        'migrate' => Migrate::class,
                        'migration:rollback' => Rollback::class,
                    ],
                ),
                new \Cekta\Migrator\Module(),
            ],
            __DIR__ . '/../../runtime/AppContainer.php',
            'App\\Runtime\\AppContainer',
            __DIR__ . '/../../runtime/discover.php',
            new Composer(
                __DIR__ . '/../../vendor/composer/autoload_classmap.php'
            )
        );
    }
}
