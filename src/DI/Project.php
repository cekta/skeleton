<?php

declare(strict_types=1);

namespace App\DI;

use Cekta\DI\ClassLoader\Composer;

class Project extends \Cekta\DI\Project
{
    public function __construct(private readonly array $env = [])
    {
        parent::__construct(
            [
                new AppModule($this->env),
                new HTTPModule(),
                new CLIModule(),
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
