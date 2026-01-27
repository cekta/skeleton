#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\DI\Project;
use Cekta\Framework\Dispatcher;

require_once __DIR__ . '/vendor/autoload.php';

$mode = $_ENV['CEKTA_MODE'] ?? 'cli';

/** @var array<string, Dispatcher> $dispatchers */
$dispatchers = [
    'build' => new Dispatcher\Build(),
    'rr' => new Dispatcher\RR(),
    'cli' => new Dispatcher\CLI(),
];

if (!array_key_exists($mode, $dispatchers)) {
    throw new RuntimeException("$mode run_mode invalid");
}

$project = new Project(getenv() + $_ENV);
$dispatchers[$mode]->dispatch($project);
