<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Cekta\Framework\Routing\Router;
use Psr\Container\ContainerInterface;

class RouterLoader extends Lazy
{
    public function __construct()
    {
        parent::__construct(function (ContainerInterface $container) {
            $router = new Router();
            $router->get('/', \App\Welcome::class);
            return $router;
        });
    }
}
