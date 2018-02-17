<?php

namespace Davesweb\Repositories\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class StubService
{
    /**
     * @var Filesystem
     */
    private $files;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @param Filesystem $files
     * @param Repository $config
     */
    public function __construct(Filesystem $files, Repository $config)
    {
        $this->files  = $files;
        $this->config = $config;
    }

    /**
     * @param string $stubName
     *
     * @throws FileNotFoundException
     *
     * @return string
     */
    public function getStubContent(string $stubName): string
    {
        $file = $this->config->get(sprintf('repositories.generator.stubs.%s', $stubName));

        return $this->files->get($file);
    }
}
