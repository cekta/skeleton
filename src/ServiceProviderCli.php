<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\LazyClosure;
use Cekta\Framework\ServiceProvider;
use Cekta\Migrator\Command\Migrate;
use Cekta\Migrator\Command\Rollback;
use Cekta\Migrator\MigrationLocator;
use Cekta\Migrator\Storage;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

readonly class ServiceProviderCli implements ServiceProvider
{
    public function __construct(private array $discover)
    {
    }

    /**
     * @inheritdoc
     */
    public function params(): array
    {
        return [
            Application::class => new LazyClosure(function (ContainerInterface $container) {
                $console = new Application();
                $console->setCommandLoader(new ContainerCommandLoader($container, [
                    'migrate' => Migrate::class,
                    'migration:rollback' => Rollback::class,
                ]));
                return $console;
            }),
            Storage\DB::class . '$table_name' => 'migrations',
            Storage\DB::class . '$column_id' => 'id',
            Storage\DB::class . '$column_class' => 'class',
            Migrate::class . '$name' => 'migrate',
            Rollback::class . '$name' => 'migration:rollback',
            '...' . MigrationLocator::class . '$migrations' => $this->discover['tags']['cekta_migrator_migrations'] ?? [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function register(): array
    {
        return [
            'containers' => [
                Migrate::class,
                Rollback::class,
            ],
            'alias' => [
                Storage::class => Storage\DB::class,
            ],
        ];
    }
}
