<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
        return [
            'alias' => [
                LoggerInterface::class => NullLogger::class
            ],
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
