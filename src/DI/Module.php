<?php
/**
 * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */

declare(strict_types=1);

namespace App\DI;

use Cekta\DI\Lazy;
use ReflectionClass;

readonly class Module implements \Cekta\DI\Module
{
    public function __construct(
        private array $env = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function onCreate(string $encoded_module): array
    {
        return [
            \Psr\Container\ContainerInterface::class => new Lazy\Container,
            \PDO::class . '$dsn' => $this->env['DB_DSN'] ?? 'sqlite:runtime/db.sqlite',
            \PDO::class . '$username' => $this->env['DB_USERNAME'] ?? null,
            \PDO::class . '$password' => $this->env['DB_PASSWORD'] ?? null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function onBuild(string $encoded_module): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function discover(ReflectionClass $class): void
    {
    }

    /**
     * @inheritDoc
     */
    public function getEncodedModule(): string
    {
        return json_encode([], JSON_PRETTY_PRINT);
    }
}
