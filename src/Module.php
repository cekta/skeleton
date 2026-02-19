<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Psr\Container\ContainerInterface;
use ReflectionClass;

readonly class Module implements \Cekta\Framework\Contract\Module
{
    /**
     * @inheritDoc
     */
    public function onCreateParameters(mixed $cachedData): array
    {
        return [
            ContainerInterface::class => new Lazy\Container()
        ];
    }

    /**
     * @inheritDoc
     */
    public function onBuildDefinitions(mixed $cachedData): array
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
    public function getCacheableData(): mixed
    {
        return [];
    }
}
