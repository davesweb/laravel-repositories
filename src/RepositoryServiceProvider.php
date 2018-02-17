<?php

namespace Davesweb\Repositories;

use Davesweb\Repositories\Console\Commands\MakeRepository;
use Davesweb\Repositories\Services\StubService;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Filesystem as FilesystemBase;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfig();
        $this->publishResources();

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    public function register()
    {
        $this->app
            ->when(StubService::class)
            ->needs(FilesystemBase::class)
            ->give(function () {
                $adapter = new Local($this->getConfig('repositories.generator.filesystem.root'));

                return new Filesystem($adapter);
            });
    }

    private function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/repositories.php' => config_path('repositories.php'),
        ], 'config');
    }

    private function publishResources()
    {
        $this->publishes([
            __DIR__ . '/../resources/stubs/' => resource_path('stubs'),
        ], 'resources');
    }

    private function registerCommands()
    {
        $this->commands([
            MakeRepository::class,
        ]);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    private function getConfig(string $key, $default = null)
    {
        /** @var Repository $config */
        $config = $this->app->make('config');

        return $config->get($key, $default);
    }
}
