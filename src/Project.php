<?php

declare(strict_types=1);

namespace App;

use Cekta\DI\Compiler;
use Cekta\DI\Lazy;
use Cekta\DI\Rule\Chain;
use Cekta\DI\Rule\Equal;
use Cekta\Framework\ContainerFactory;
use Cekta\Framework\HttpApplication;
use Cekta\Framework\Pipeline;
use Cekta\Framework\ProjectDiscovery;
use Cekta\Framework\Routing\Matcher;
use Cekta\Framework\Routing\Router;
use Cekta\Framework\ServiceProvider;
use Cekta\Migrator\Migration;
use Cekta\Routing\MatcherInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Project implements ContainerFactory
{
    public readonly string $project_dir;
    public readonly string $container_file;
    public readonly string $container_fqcn;
    private array $params = [];
    private readonly string $project_runtime;
    private $tag_migrations = 'cekta_migrator_migrations';
    /**
     * @var ServiceProvider[]
     */
    private array $providers;

    public function __construct()
    {
        $this->project_dir = realpath(__DIR__ . '/..');
        $this->container_file = $this->project_dir . '/runtime/Container.php';
        $this->container_fqcn = 'App\\Runtime\\Container';
        $this->project_runtime = $this->project_dir . '/runtime/project.php';

        $this->providers[] = new CliServiceProvider($this->tag_migrations);
    }

    public function prepare(): static
    {
        if ($this->toBeCompiled()) {
            $this->compile();
        }
        return $this;
    }

    public function createContainer(): ContainerInterface
    {
        return new ($this->container_fqcn)($this->params);
    }

    private function toBeCompiled(): bool
    {
        return true; // you logic, now every time at start
    }

    public function params(): array
    {
        if (empty($this->params)) {
            $runtime = $this->readRuntime();
            $this->params = [
                'db_dsn' => 'sqlite:db.sqlite',
                'db_username' => null,
                'db_password' => null,
                'db_options' => null,
                ContainerInterface::class => new Lazy(function (ContainerInterface $container) {
                    return $container;
                }),
                Router::class => new RouterLoader(),
                $this->tag_migrations => $runtime['tags'][$this->tag_migrations] ?? [],
            ];
            foreach ($this->providers as $provider) {
                $this->params += $provider->params();
            }
        }
        return $this->params;
    }

    private function compile(): void
    {
        $items = array_keys(require "{$this->project_dir}/vendor/composer/autoload_classmap.php");
        $discover = new ProjectDiscovery($items)
            ->containerImplement(RequestHandlerInterface::class, [Pipeline::class])
            ->containerImplement(Migration::class)
            ->tagImplement($this->tag_migrations, Migration::class)
            ->makeResult();

        $this->writeRuntime([
            'tags' => $discover['tags'],
        ]);

        $provider_configuration = [];
        foreach ($this->providers as $provider) {
            $provider_configuration += $provider->register();
        }

        $rules = [
            new Equal(\PDO::class, [
                'dsn' => 'db_dsn',
                'username' => 'db_username',
                'password' => 'db_password',
                'options' => 'db_options',
            ]),
        ];
        $content = new Compiler(
            containers: array_merge(
                $discover['containers'],
                $provider_configuration['containers'] ?? []
            ),
            params: $this->params(),
            alias: [
                RequestHandlerInterface::class => HttpApplication::class,
                MatcherInterface::class => Matcher::class,
                ...($provider_configuration['alias'] ?? [])
            ],
            fqcn: $this->container_fqcn,
            rule: new Chain(...array_merge($rules, ($provider_configuration['rules'] ?? []))),
        )->compile();
        
        if (file_put_contents($this->container_file, $content, LOCK_EX) === false) {
            throw new \RuntimeException("$this->container_file cant compile");
        }
    }

    private function writeRuntime(array $data): void
    {
        $content = '<?php return ' . var_export($data, true) . ';';
        if (file_put_contents($this->project_runtime, $content, LOCK_EX) === false) {
            throw new \RuntimeException("$this->project_runtime cant be cached");
        }
    }

    private function readRuntime(): array
    {
        if (!is_file($this->project_runtime)) {
            throw new \RuntimeException("$this->project_runtime invalid filename");
        }
        $result = include $this->project_runtime;
        if (!is_array($result)) {
            throw new \RuntimeException("$this->project_runtime must contain array");
        }
        return $result;
    }

}
