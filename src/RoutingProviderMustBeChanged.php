<?php

declare(strict_types=1);

namespace App;

use App\Handler\Hello;
use App\Handler\NotFound;
use Cekta\Framework\HandlerLocator;
use Cekta\Framework\MiddlewareLocator;
use Cekta\Routing\Nikic\DispatcherBuilder;
use Cekta\Routing\Nikic\Handler;
use Cekta\Routing\Nikic\Matcher;

class RoutingProviderMustBeChanged extends Matcher
{
    public function __construct(
        HandlerLocator $providerHandler,
        MiddlewareLocator $providerMiddleware
    ) {
        $builder = new DispatcherBuilder();
        $builder->get('/', Hello::class);
        parent::__construct(
            new Handler(NotFound::class),
            $builder->build(),
            $providerHandler,
            $providerMiddleware
        );
    }

}
