<?php
/**
 * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */

declare(strict_types=1);

namespace App\DI;

use Cekta\DI\LazyClosure;
use Cekta\DI\Module;
use Psr\Container\ContainerInterface;

readonly class AppModule implements Module
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
            \Psr\Container\ContainerInterface::class => new LazyClosure(function (ContainerInterface $container) {
                return $container;
            }),
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
    public function onDiscover(array $classes): string
    {
        $state = [];
        return json_encode($state, JSON_PRETTY_PRINT);
    }
}
