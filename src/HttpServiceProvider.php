<?php

declare(strict_types=1);

namespace App;

use Cekta\Framework\Application;
use Cekta\Framework\HttpApplication;
use Cekta\Framework\Routing\Matcher;
use Cekta\Framework\Routing\Router;
use Cekta\Framework\ServiceProvider;
use Cekta\Routing\MatcherInterface;
use Psr\Http\Server\RequestHandlerInterface;


class HttpServiceProvider implements ServiceProvider
{
    public function register(Application $app): Application
    {
        return $app
            ->param(Router::class, $this->buildRouter())
            ->alias(RequestHandlerInterface::class, HttpApplication::class)
            ->alias(MatcherInterface::class, Matcher::class);
    }

    public function buildRouter(): Router
    {
        $router = new Router();
        $router->get('/', \App\Welcome::class);
        return $router;
    }
}
