<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Compiler;
use Cekta\Framework\ContainerFactory;
use Cekta\Framework\Pipeline;
use Cekta\Framework\ProjectDiscovery;
use Cekta\Framework\ServiceProvider;
use Cekta\Framework\ServiceProviderChain;
use Cekta\Migrator\Migration;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Project implements ContainerFactory
{
    private readonly string $project_dir;
    private readonly string $container_file;
    private readonly string $container_fqcn;
    private readonly int $container_permission;
    private string $tag_migrations = 'cekta_migrator_migrations';
    private ServiceProvider $provider;

    public function __construct(private readonly array $env)
    {
        $this->project_dir = realpath(__DIR__ . '/..');
        $this->container_file = $this->project_dir . '/runtime/Container.php';
        $this->container_fqcn = 'App\\Runtime\\Container';
        $this->container_permission = 0777;

        $items = array_keys(require "{$this->project_dir}/vendor/composer/autoload_classmap.php");
        $discover = new ProjectDiscovery($items)
            ->containerImplement(RequestHandlerInterface::class, [Pipeline::class])
            ->containerImplement(Migration::class)
            ->tagImplement($this->tag_migrations, Migration::class)
            ->makeResult();
        
        $this->provider = new ServiceProviderChain(
            new ServiceProviderApp($this->env),
            new ServiceProviderCli($discover['tags'][$this->tag_migrations] ?? []),
            new ServiceProviderHttp(),
        );
        
        $this->compile($discover['containers']);
    }

    public function createContainer(): ContainerInterface
    {
        return new ($this->container_fqcn)($this->provider->params());
    }

    private function compile(array $containers): void
    {
        $provider_configuration = $this->provider->register();
        $content = new Compiler(
            containers: array_merge($containers, $provider_configuration['containers'] ?? []),
            params: $this->provider->params(),
            alias: $provider_configuration['alias'] ?? [],
            fqcn: $this->container_fqcn,
        )->compile();
        if (file_put_contents($this->container_file, $content, LOCK_EX) === false) {
            throw new \RuntimeException("$this->container_file cant compile");
        }
        chmod($this->container_file, $this->container_permission);
    }
}
