<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\LazyClosure;
use Cekta\Framework\HttpApplication;
use Cekta\Framework\Routing\NotAllowed;
use Cekta\Framework\Routing\NotFound;
use Cekta\Framework\ServiceProvider;
use Cekta\Routing\MatcherInterface;
use Cekta\Routing\Nikic\Matcher;
use Cekta\Routing\Nikic\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ServiceProviderHttp implements ServiceProvider
{
    public function params(): array
    {
        return [
            Router::class => new LazyClosure(function (ContainerInterface $container) {
                $router = new Router(NotFound::class, NotAllowed::class);
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
            'singletons' => [
                Router::class,
            ],
        ];
    }
}
