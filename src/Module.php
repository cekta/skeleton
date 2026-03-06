<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

namespace App;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;

readonly class Module implements \Cekta\Module\Module
{
    /**
     * @param array<string, string> $env
     */
    public function __construct(
        private array $env,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function onCreateParameters(mixed $cachedData): array
    {
        return [
            ContainerInterface::class => new \Cekta\DI\Lazy\Container(),
//            \PDO::class . '$username' => $this->env['DB_USERNAME'] ?? 'postgres',
//            \PDO::class . '$password' => $this->env['DB_PASSWORD'] ?? 'cekta',
//            \PDO::class . '$dsn' => new \Cekta\DI\Lazy\Closure(function (ContainerInterface $c) {
//                $host = $this->env['DB_HOST'] ?? 'db';
//                $db = $this->env['DB_NAME'] ?? 'postgres';
//                return "pgsql:host=$host;dbname=$db;";
//            }),
//            \PDO::class . '$options' => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function onBuildDefinitions(mixed $cachedData): array
    {
        return [
            'entries' => [],
            'alias' => [
                LoggerInterface::class => NullLogger::class,
            ],
            'singletons' => [],
            'factories' => [],
        ];
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
    public function getCacheableData(): mixed
    {
        return [];
    }
}
