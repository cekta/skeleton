<?php
/**
 * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */

declare(strict_types=1);

namespace App\DI;

use App\CLICommandMapProvider;
use App\HTTPRouteProvider;
use Cekta\DI\LazyClosure;
use Cekta\DI\Module;
use Cekta\Framework\HTTP\Application;
use Cekta\Migrator\Storage;
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
        $state = json_decode($encoded_module, true);
        return [
            \Cekta\Routing\Nikic\Router::class => new HTTPRouteProvider(),
            \Cekta\Framework\CLI\ContainerCommandLoader::class . '$commandMap' => new CLICommandMapProvider(),
            \Psr\Container\ContainerInterface::class => new LazyClosure(function (ContainerInterface $container) {
                return $container;
            }),
            '...' . \Cekta\Migrator\MigrationLocator::class . '$migrations' => $state[\Cekta\Migrator\Migration::class] ?? [],
            \PDO::class . '$dsn' => $this->env['DB_DSN'] ?? 'sqlite:db.sqlite',
            \PDO::class . '$username' => $this->env['DB_USERNAME'] ?? null,
            \PDO::class . '$password' => $this->env['DB_PASSWORD'] ?? null,
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
                \Cekta\Framework\CLI\Application::class,
                \Psr\Http\Server\RequestHandlerInterface::class,
                ...($state[\Psr\Http\Server\RequestHandlerInterface::class] ?? []),
                ...($state[\Cekta\Migrator\Migration::class] ?? []),
                ...($state[\Symfony\Component\Console\Command\Command::class] ?? []),
            ],
            'alias' => [
                \Psr\Http\Server\RequestHandlerInterface::class => Application::class,
                \Cekta\Routing\MatcherInterface::class => \Cekta\Routing\Nikic\Matcher::class,
                Storage::class => Storage\DB::class,
            ],
            'singletons' => [
                \Cekta\Routing\Nikic\Router::class,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function onDiscover(array $classes): string
    {
        $state = [
            \Psr\Http\Server\RequestHandlerInterface::class => [],
            \Cekta\Migrator\Migration::class => [],
            \Symfony\Component\Console\Command\Command::class => [],
        ];

        foreach ($classes as $class) {
            if (
                $class->implementsInterface(\Psr\Http\Server\RequestHandlerInterface::class) 
                && $class->isInstantiable()
            ) {
                $state[\Psr\Http\Server\RequestHandlerInterface::class][] = $class->name;
            }

            if (
                $class->implementsInterface(\Cekta\Migrator\Migration::class) 
                && $class->isInstantiable()
            ) {
                $state[\Cekta\Migrator\Migration::class][] = $class->name;
            }

            if (
                $class->isSubclassOf(\Symfony\Component\Console\Command\Command::class) 
                && $class->isInstantiable()
                && !str_starts_with($class->name, "Symfony\\Component\\Console\\")
            ) {
                $state[\Symfony\Component\Console\Command\Command::class][] = $class->name;
            }
        }

        return json_encode($state, JSON_PRETTY_PRINT);
    }
}
