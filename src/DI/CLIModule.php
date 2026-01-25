<?php

declare(strict_types=1);

namespace App\DI;

use Cekta\DI\Module;
use Cekta\Framework\CLI\Application;
use Cekta\Framework\CLI\ContainerCommandLoader;
use Cekta\Migrator\Command\Migrate;
use Cekta\Migrator\Command\Rollback;
use Cekta\Migrator\Migration;
use Cekta\Migrator\MigrationLocator;
use Cekta\Migrator\Storage;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;

class CLIModule implements Module
{
    private array $state;
    
    /**
     * @inheritDoc
     */
    public function onCreate(string $encoded_module): array
    {
        $state = json_decode($encoded_module, true);
        return [
            ContainerCommandLoader::class . '$commandMap' => [
                'migrate' => Migrate::class,
                'migration:rollback' => Rollback::class,
            ],
            '...' . MigrationLocator::class . '$migrations' => $state[Migration::class] ?? [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function onBuild(string $encoded_module): array
    {
        $state = json_decode($encoded_module, true);
        return [
            'entries' => [
                Application::class,
                ...($state[Command::class] ?? []),
                ...($state[Migration::class] ?? []),
            ],
            'alias' => [
                Storage::class => Storage\DB::class,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function discover(ReflectionClass $class): void
    {
        if (
            $class->isSubclassOf(Command::class)
            && $class->isInstantiable()
            && !str_starts_with($class->name, "Symfony\\Component\\Console\\")
        ) {
            $this->state[Command::class][] = $class->name;
        }

        if (
            $class->implementsInterface(Migration::class)
            && $class->isInstantiable()
        ) {
            $this->state[Migration::class][] = $class->name;
        }
    }

    /**
     * @inheritDoc
     */
    public function getEncodedModule(): string
    {
        return json_encode($this->state, JSON_PRETTY_PRINT);
    }
}
