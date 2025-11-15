<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Cekta\Framework\Routing\Router;
use Psr\Container\ContainerInterface;

class RouterLoader implements Lazy
{
    public function load(ContainerInterface $container): mixed
    {
        $router = new Router();
        $router->get('/', \App\Welcome::class);
        return $router;
    }
}
