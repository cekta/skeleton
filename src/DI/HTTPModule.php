<?php

declare(strict_types=1);

namespace App\DI;

use App\Welcome;
use Cekta\DI\LazyClosure;
use Cekta\DI\Module;
use Cekta\Framework\HTTP\Application;
use Cekta\Framework\HTTP\NotAllowed;
use Cekta\Framework\HTTP\NotFound;
use Cekta\Routing\MatcherInterface;
use Cekta\Routing\Nikic\Matcher;
use Cekta\Routing\Nikic\Router;
use Psr\Http\Server\RequestHandlerInterface;

class HTTPModule implements Module
{
    /**
     * @inheritDoc
     */
    public function onCreate(string $encoded_module): array
    {
        return [
            Router::class => new LazyClosure(function () {
                $router = new Router(NotFound::class, NotAllowed::class);
                $router->get('/', Welcome::class);
                // your handlers must be registered here
                return $router;
            }),
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
                RequestHandlerInterface::class,
                ...($state[RequestHandlerInterface::class] ?? []),
            ],
            'alias' => [
                RequestHandlerInterface::class => Application::class,
                MatcherInterface::class => Matcher::class,
            ],
            'singletons' => [
                Router::class,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function onDiscover(array $classes): string
    {
        $state = [
            RequestHandlerInterface::class => [],
        ];
        foreach ($classes as $class) {
            if (
                $class->implementsInterface(RequestHandlerInterface::class)
                && $class->isInstantiable()
            ) {
                $state[RequestHandlerInterface::class][] = $class->name;
            }
        }
        return json_encode($state, JSON_PRETTY_PRINT);
    }
}
