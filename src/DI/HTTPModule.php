<?php

declare(strict_types=1);

namespace App\DI;

use App\Welcome;
use Cekta\DI\Lazy;
use Cekta\DI\Module;
use Cekta\Framework\HTTP\Application;
use Cekta\Framework\HTTP\NotAllowed;
use Cekta\Framework\HTTP\NotFound;
use Cekta\Routing\MatcherInterface;
use Cekta\Routing\Nikic\Matcher;
use Cekta\Routing\Nikic\Router;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;

class HTTPModule implements Module
{
    private array $state;
    /**
     * @inheritDoc
     */
    public function onCreate(string $encoded_module): array
    {
        return [
            Router::class => new Lazy\Closure(function () {
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
    public function discover(ReflectionClass $class): void
    {
        if (
            $class->implementsInterface(RequestHandlerInterface::class)
            && $class->isInstantiable()
        ) {
            $this->state[RequestHandlerInterface::class][] = $class->name;
        }
    }

    /**
     * @inheritDoc
     */
    public function getEncodedModule(): string
    {
        return json_encode($this->state, JSON_PRETTY_PRINT);
    }
}
