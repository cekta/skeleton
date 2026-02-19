#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Project;
use Cekta\Framework\Dispatcher;

require_once __DIR__ . '/vendor/autoload.php';

$env = getenv() + $_ENV;
$mode = $env['CEKTA_MODE'] ?? 'cli';

/** @var array<string, Dispatcher> $dispatchers */
$dispatchers = [
    'build' => new Dispatcher\Build(),
    'rr' => new Dispatcher\RR(),
    'cli' => new Dispatcher\CLI(),
];

if (!array_key_exists($mode, $dispatchers)) {
    throw new RuntimeException("CEKTA_MODE=$mode invalid");
}

$project = new Project($env);
$dispatchers[$mode]->dispatch($project);
