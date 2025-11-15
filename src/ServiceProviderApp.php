<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\LazyClosure;
use Cekta\Framework\ServiceProvider;
use Psr\Container\ContainerInterface;

class ServiceProviderApp implements ServiceProvider
{
    public function __construct(private readonly array $env)
    {
    }

    public function params(): array
    {
        return [
            \PDO::class . '$dsn' => $this->env['DB_DSN'] ?? 'sqlite:db.sqlite',
            \PDO::class . '$username' => $this->env['DB_USERNAME'] ?? null,
            \PDO::class . '$password' => $this->env['DB_PASSWORD'] ?? null,
            \PDO::class . '$options' => null,
            ContainerInterface::class => new LazyClosure(function (ContainerInterface $container) {
                return $container;
            }),
        ];
    }

    public function register(): array
    {
        return [
            'containers' => [],
            'alias' => [],
        ];
    }
}
