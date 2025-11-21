<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Compiler;
use Cekta\Framework\FilePHP;
use Cekta\Framework\Pipeline;
use Cekta\Framework\ProjectDiscovery;
use Cekta\Framework\ServiceProvider;
use Cekta\Framework\ServiceProviderChain;
use Cekta\Migrator\Migration;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

readonly class Project implements \Cekta\Framework\Project
{
    private string $project_dir;
    private string $container_file;
    private string $container_fqcn;
    private int $container_permission;
    private ServiceProvider $provider;
    private FilePHP $runtime;

    public function __construct(private array $env)
    {
        $this->project_dir = realpath(__DIR__ . '/..');
        $this->container_file = $this->project_dir . '/runtime/Container.php';
        $this->container_fqcn = 'App\\Runtime\\Container';
        $this->container_permission = 0777;
        
        $this->runtime = new FilePHP($this->project_dir . '/runtime/project.php');
        
        $this->provider = new ServiceProviderChain(
            new ServiceProviderApp($this->env),
            new ServiceProviderCli($this->runtime->read()),
            new ServiceProviderHttp(),
        );
    }

    public function createContainer(): ContainerInterface
    {
        if (!class_exists($this->container_fqcn)) {
            throw new RuntimeException("$this->container_fqcn class not found, maybe need generate?");
        }
        return new ($this->container_fqcn)($this->provider->params());
    }

    public function compile(): void
    {
        $items = array_keys(require "{$this->project_dir}/vendor/composer/autoload_classmap.php");
        $discover = new ProjectDiscovery($items)
            ->containerImplement(RequestHandlerInterface::class, [Pipeline::class])
            ->containerImplement(Migration::class)
            ->tagImplement('cekta_migrator_migrations', Migration::class)
            ->makeResult();
        $this->runtime->write($discover);
        $provider_configuration = $this->provider->register();
        $content = new Compiler(
            containers: array_merge($discover['containers'] ?? [], $provider_configuration['containers'] ?? []),
            params: $this->provider->params(),
            alias: $provider_configuration['alias'] ?? [],
            fqcn: $this->container_fqcn,
            singletons: $provider_configuration['singletons'] ?? [],
            factories: $provider_configuration['factories'] ?? [],
        )->compile();
        if (file_put_contents($this->container_file, $content, LOCK_EX) === false) {
            throw new RuntimeException("$this->container_file cant compile");
        }
        chmod($this->container_file, $this->container_permission);
    }
}
