<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

namespace App;

class Application
{
    public static function main(): int
    {
        $app = new \Cekta\Framework\Application([
            'build' => new \Cekta\Framework\Dispatcher\Build(),
            'rr' => new \Cekta\Framework\Dispatcher\RR(),
            'cli' => new \Cekta\Framework\Dispatcher\CLI(),
        ]);

        $env = getenv() + $_ENV;
        
        return $app->handle(
            $env['CEKTA_MODE'] ?? 'cli',
            new \Cekta\Framework\Project(
                [
                    new Module(),
                    new \Cekta\Framework\HTTP\Module(),
                    new \Cekta\Framework\CLI\Module(),
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
