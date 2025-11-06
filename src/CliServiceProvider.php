<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Lazy;
use Cekta\Framework\Application;
use Cekta\Framework\ServiceProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

class CliServiceProvider implements ServiceProvider
{
    private const string CEKTA_CONSOLE_COMMAND_MAP = 'CEKTA_CONSOLE_COMMAND_MAP';
    public function register(Application $app): Application
    {
        return $app->param(
            \Symfony\Component\Console\Application::class,
            new Lazy(function (ContainerInterface $c) {
                $console = new \Symfony\Component\Console\Application();
                $console->setCommandLoader(new ContainerCommandLoader($c, $c->get(static::CEKTA_CONSOLE_COMMAND_MAP)));
                return $console;
            })
        )
            ->param(static::CEKTA_CONSOLE_COMMAND_MAP, [
                // you symfony/console command here, alias => FQCN Command
            ]);
    }
}
