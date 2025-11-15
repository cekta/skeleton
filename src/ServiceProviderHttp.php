<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\LazyClosure;
use Cekta\Framework\HttpApplication;
use Cekta\Framework\Routing\Matcher;
use Cekta\Framework\Routing\Router;
use Cekta\Framework\ServiceProvider;
use Cekta\Routing\MatcherInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ServiceProviderHttp implements ServiceProvider
{
    public function params(): array
    {
        return [
            Router::class => new LazyClosure(function (ContainerInterface $container) {
                $router = new Router();
                $router->get('/', \App\Welcome::class);
                return $router;
            }),
        ];
    }

    public function register(): array
    {
        return [
            'containers' => [],
            'alias' => [
                RequestHandlerInterface::class => HttpApplication::class,
                MatcherInterface::class => Matcher::class,
            ],
        ];
    }
}
