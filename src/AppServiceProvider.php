<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Cekta\Framework\Application;
use Cekta\Framework\DiscoverServiceProvider;
use Cekta\Framework\Pipeline;
use Cekta\Framework\ServiceProvider;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AppServiceProvider implements ServiceProvider
{
    
    private array $providers = [];

    public function __construct()
    {
        $this->providers[] = $this->createDiscoverServiceProvider();
        $this->providers[] = new HttpServiceProvider();
        $this->providers[] = new CliServiceProvider();
    }

    public function register(Application $app): Application
    {
        foreach ($this->providers as $provider) {
            $app = $provider->register($app);
        }

        return $app
            ->param(
                ContainerInterface::class,
                new Lazy(function (ContainerInterface $container) {
                    return $container;
                })
            )
            ->setContainerFilename(__DIR__ . '/../runtime/Container.php');
    }

    private function createDiscoverServiceProvider(): ServiceProvider
    {
        $items = array_keys(require __DIR__ . '/../vendor/composer/autoload_classmap.php');
        $discover = new DiscoverServiceProvider($items);
        return $discover->containerImplement(RequestHandlerInterface::class, [Pipeline::class]);
    }
}
