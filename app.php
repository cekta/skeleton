<?php

declare(strict_types=1);

use App\AppServiceProvider;
use Cekta\Framework\Dispatcher\HTTP;
use Spiral\RoadRunner\Environment;

require_once __DIR__ . '/vendor/autoload.php';

$dispatchers = [
    Environment\Mode::MODE_HTTP => new HTTP(),
];

$mode = Environment::fromGlobals()->getMode();
if (!array_key_exists($mode, $dispatchers)) {
    throw new RuntimeException("$mode invalid");
}

$dispatchers[$mode]->serve(new AppServiceProvider());

