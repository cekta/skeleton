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
     * @inheritDoc
     */
    public function onCreate(string $encoded_module): array
    {
        return [
            ContainerInterface::class => new Lazy\Container()
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
