<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Cekta\Framework\HTTP\NotAllowed;
use Cekta\Framework\HTTP\NotFound;
use Cekta\Routing\Nikic\Router;
use Psr\Container\ContainerInterface;

class HTTPRouteProvider implements Lazy
{
    public function load(ContainerInterface $container): Router
    {
        $router = new Router(NotFound::class, NotAllowed::class);
        $router->get('/', Welcome::class);
        return $router;
    }
}
