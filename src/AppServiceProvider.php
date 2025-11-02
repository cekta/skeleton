<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Cekta\Framework\Application;
use Cekta\Framework\DiscoverServiceProvider;
use Cekta\Framework\Pipeline;
use Cekta\Framework\ServiceProvider;
use Psr\Container\ContainerInterface;

class AppServiceProvider implements ServiceProvider
{
    private array $providers = [];
    private array $alias = [
        \Psr\Http\Server\RequestHandlerInterface::class => \Cekta\Framework\HttpApplication::class,
        \Cekta\Routing\MatcherInterface::class => \App\RoutingProviderMustBeChanged::class,
    ];

    public function __construct()
    {
        $items = array_keys(require __DIR__ . '/../vendor/composer/autoload_classmap.php');
        $this->providers[] = new DiscoverServiceProvider(
            array_filter($items, function (string $item): bool {
                return $item !== Pipeline::class; // exclude
            })
        );
    }

    public function register(Application $app): Application
    {
        foreach ($this->providers as $provider) {
            $app = $provider->register($app);
        }

        foreach ($this->alias as $name => $target) {
            $app->alias($name, $target);
        }

        $app->param(ContainerInterface::class, new Lazy(function (ContainerInterface $container) {
            return $container;
        }));

        $app->setContainerFilename(__DIR__ . '/../runtime/Container.php');

        return $app;
    }
}
