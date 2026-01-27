<?php

declare(strict_types=1);

namespace App\DI;

use Cekta\DI\ClassLoader\Composer;

class Project extends \Cekta\DI\Project
{
    /**
     * @param array<string, string> $env
     */
    public function __construct(private readonly array $env = [])
    {
        // load config from env
        parent::__construct(
            [
                new Module(),
                new \Cekta\Framework\HTTP\Module(),
                new \Cekta\Framework\CLI\Module(),
            ],
            __DIR__ . '/../../runtime/AppContainer.php',
            'App\\Runtime\\AppContainer',
            __DIR__ . '/../../runtime/discover.php',
            new Composer(
                __DIR__ . '/../../vendor/composer/autoload_classmap.php'
            )
        );
    }
}
