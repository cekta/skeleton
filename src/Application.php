<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

namespace App;

use Cekta\Migrator\Command\MakeMigration;
use Cekta\Migrator\Command\Migrate;
use Cekta\Migrator\Command\Rollback;

class Application
{
    public static function main(): int
    {
        $app = new \Cekta\Framework\Application([
            'build' => new \Cekta\Framework\Dispatcher\Build(),
            'rr' => new \Cekta\RoadRunner\Dispatcher\RR(),
            'cli' => new \Cekta\CliSymfony\Dispatcher(),
        ]);
        $env = getenv() + $_ENV;
        return $app->handle(
            $env['CEKTA_MODE'] ?? 'cli',
            new \Cekta\Framework\Project(
                [
                    new Module($env),
                    new \Cekta\RoadRunner\Module(),
                    new \Cekta\CliSymfony\Module([
                        'migrate' => Migrate::class,
                        'rollback' => Rollback::class,
                        'make:migration' => MakeMigration::class,
                    ]),
                    new \Cekta\Migrator\Module(),
                ],
                __DIR__ . '/../runtime/AppContainer.php',
                'App\\Runtime\\AppContainer',
                __DIR__ . '/../runtime/discover.json',
                new \Cekta\Framework\ClassLoader\Composer(
                    __DIR__ . '/../vendor/composer/autoload_classmap.php'
                )
            ),
        );
    }
}
