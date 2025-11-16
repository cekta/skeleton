#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Project;
use Cekta\Framework\Dispatcher;
use Spiral\RoadRunner\Environment;

require_once __DIR__ . '/vendor/autoload.php';

/** @var array<string, Dispatcher> $dispatchers */
$dispatchers = [
    Environment\Mode::MODE_HTTP => new Dispatcher\HTTP(),
    'cli' => new Dispatcher\Cli(),
    'compile' => new Dispatcher\Compile(),
];

if (!empty($_ENV['CEKTA_MODE'])) {
    $mode = $_ENV['CEKTA_MODE'];
} elseif (!empty(Environment::fromGlobals()->getMode())) {
    $mode = Environment::fromGlobals()->getMode();
} else {
    $mode = php_sapi_name();
}

if (!array_key_exists($mode, $dispatchers)) {
    throw new RuntimeException("$mode run_mode invalid");
}

$dispatchers[$mode]->serve(new Project($_ENV));
