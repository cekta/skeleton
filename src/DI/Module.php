<?php

declare(strict_types=1);

namespace App\DI;

use Cekta\DI\Lazy;
use PDO;
use Psr\Container\ContainerInterface;
use ReflectionClass;

readonly class Module implements \Cekta\DI\Module
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private array $config = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function onCreate(string $encoded_module): array
    {
        return [
            ContainerInterface::class => new Lazy\Container(),
            PDO::class . '$dsn' => $this->config['DB_DSN'] ?? 'sqlite:runtime/db.sqlite',
            PDO::class . '$username' => $this->config['DB_USERNAME'] ?? null,
            PDO::class . '$password' => $this->config['DB_PASSWORD'] ?? null,
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
        return json_encode([], JSON_PRETTY_PRINT) ?: '';
    }
}
