#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Project;
use Cekta\Framework\Dispatcher;
use Cekta\Framework\Dispatcher\Cli;
use Cekta\Framework\Dispatcher\HTTP;
use Spiral\RoadRunner\Environment;

require_once __DIR__ . '/vendor/autoload.php';

/** @var array<string, Dispatcher> $dispatchers */
$dispatchers = [
    Environment\Mode::MODE_HTTP => new HTTP(),
    'cli' => new Cli(),
];

$mode = Environment::fromGlobals()->getMode();
if (empty($mode)) {
    $mode = php_sapi_name(); // not in rr mode
}

if (!array_key_exists($mode, $dispatchers)) {
    throw new RuntimeException("$mode invalid");
}

$dispatchers[$mode]->serve(new Project($_ENV));
