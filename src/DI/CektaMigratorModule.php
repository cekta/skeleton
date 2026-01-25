<?php

declare(strict_types=1);

namespace App\DI;

use Cekta\DI\Module;
use Cekta\Migrator\Migration;
use Cekta\Migrator\MigrationLocator;
use Cekta\Migrator\Storage;
use ReflectionClass;

/**
 * this module will be moved to cekta/migrator (tmp solution)
 */
class CektaMigratorModule implements Module
{
    private array $state = [];
    
    public function __construct(
        private readonly string $storage = Storage\DB::class
    ) {
    }

    /**
     * @inheritDoc
     */
    public function onCreate(string $encoded_module): array
    {
        $state = json_decode($encoded_module, true);
        return [
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
                ...($state[Migration::class] ?? []),
            ],
            'alias' => [
                Storage::class => $this->storage,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function discover(ReflectionClass $class): void
    {
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
