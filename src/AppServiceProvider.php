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

    public function __construct()
    {
        $items = array_keys(require __DIR__ . '/../vendor/composer/autoload_classmap.php');
        $this->providers[] = new DiscoverServiceProvider(
            array_filter($items, function (string $item): bool {
                return $item !== Pipeline::class; // exclude
            })
        );
        $this->providers[] = new RoutingServiceProvider();
    }

    public function register(Application $app): Application
    {
        foreach ($this->providers as $provider) {
            $app = $provider->register($app);
        }

        return $app->param(
            ContainerInterface::class,
            new Lazy(function (ContainerInterface $container) {
                return $container;
            })
        )
            ->alias(\Psr\Http\Server\RequestHandlerInterface::class, \Cekta\Framework\HttpApplication::class)
            ->setContainerFilename(__DIR__ . '/../runtime/Container.php');
    }
}
