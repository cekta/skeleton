<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Cekta\DI\Rule\Equal;
use Cekta\DI\Rule\StartWith;
use Cekta\Framework\ServiceProvider;
use Cekta\Migrator\Command\Migrate;
use Cekta\Migrator\Command\Rollback;
use Cekta\Migrator\Storage;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

class CliServiceProvider implements ServiceProvider
{
    
    private string $cekta_migrator_migrate_name = 'cekta_migrator_migrate_name';
    private string $cekta_migrator_rollback_name = 'cekta_migrator_rollback_name';
    public function __construct(
        private string $tag_migrations
    ) {
    }

    /**
     * @inheritdoc
     */
    public function params(): array
    {
        return [
            Application::class => new Lazy(function (ContainerInterface $container) {
                $console = new Application();
                $console->setCommandLoader(new ContainerCommandLoader($container, [
                    $container->get($this->cekta_migrator_migrate_name) => Migrate::class,
                    $container->get($this->cekta_migrator_rollback_name) => Rollback::class,
                ]));
                return $console;
            }),
            $this->cekta_migrator_migrate_name => 'migrate',
            $this->cekta_migrator_rollback_name => 'migration:rollback',
            Storage::class => new Lazy(function (ContainerInterface $container) {
                return new Storage\DB($container->get(\PDO::class));
            }),
        ];
    }

    /**
     * @inheritdoc
     */
    public function register(): array
    {
        $rules = [
            new StartWith('Cekta\\Migrator\\', [
                '...migrations' => $this->tag_migrations,
            ]),
            new Equal(Migrate::class, [
                'name' => $this->cekta_migrator_migrate_name,
            ]),
            new Equal(Rollback::class, [
                'name' => $this->cekta_migrator_rollback_name,
            ])
        ];
        return [
            'containers' => [
                Migrate::class,
                Rollback::class,
                \PDO::class,
            ],
            'rules' => $rules,
        ];
    }
}
