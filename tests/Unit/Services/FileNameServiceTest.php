<?php

namespace Davesweb\Repositories\Tests\Unit\Services;

use Davesweb\Repositories\Services\FileNameService;
use Davesweb\Repositories\Services\SimpleVariableReplacer;
use Davesweb\Repositories\Tests\TestCase;
use Illuminate\Contracts\Config\Repository;
use Mockery\Mock;

class FileNameServiceTest extends TestCase
{
    /**
     * @var Repository|Mock
     */
    private $config;

    /**
     * @var SimpleVariableReplacer|Mock
     */
    private $variablesReplacer;

    /**
     * @var string
     */
    private $fileName = '/app/{namespace}/Other/{name}.php';

    /**
     * @var string
     */
    private $fileNameWithImplementation = '/app/{namespace}/Other/{implementation}/{name}.php';

    /**
     * @var string
     */
    private $prefix = 'Prefix';

    /**
     * @var string
     */
    private $suffix = 'Suffix';

    /**
     * @var FileNameService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();

        $this->config            = $this->mock(Repository::class);
        $this->variablesReplacer = $this->mock(SimpleVariableReplacer::class);

        $this->service = new FileNameService($this->config, $this->variablesReplacer);
    }

    public function test_it_returns_a_filename_with_variables_replaced_without_implementation()
    {
        $type      = 'interface';
        $name      = 'TestRepository';
        $namespace = 'Test\\Namespace';

        $variables = [
            'name'      => $this->prefix . $name . $this->suffix,
            'namespace' => $namespace,
        ];

        $this->setupConfig($type);
        $this->setupReplacer($variables);

        $actualFileName = $this->service->getFilename($type, $name, $namespace);

        $this->assertEquals($this->getReplacedFilename($variables), $actualFileName);
    }

    public function test_it_returns_a_filename_with_variables_replaced_with_implementation()
    {
        $type           = 'interface';
        $name           = 'TestRepository';
        $namespace      = 'Test\\Namespace';
        $implementation = 'Eloquent';

        $variables = [
            'name'           => $this->prefix . $name . $this->suffix,
            'namespace'      => $namespace,
            'implementation' => $implementation,
        ];

        $this->setupConfig($type, $implementation);
        $this->setupReplacer($variables, $implementation);

        $actualFileName = $this->service->getFilename($type, $name, $namespace, $implementation);

        $this->assertEquals($this->getReplacedFilename($variables, $implementation), $actualFileName);
    }

    /**
     * @param string      $type
     * @param string|null $implementation
     */
    private function setupConfig(string $type, string $implementation = null)
    {
        $this->config
            ->shouldReceive($this->method([Repository::class, 'get']))
            ->atLeast()
            ->once()
            ->with(sprintf('repositories.generator.%s_path', $type))
            ->andReturn(null === $implementation ? $this->fileName : $this->fileNameWithImplementation);

        $this->config
            ->shouldReceive($this->method([Repository::class, 'get']))
            ->atLeast()
            ->once()
            ->with(sprintf('repositories.generator.%s_prefix', $type))
            ->andReturn($this->prefix);

        $this->config
            ->shouldReceive($this->method([Repository::class, 'get']))
            ->atLeast()
            ->once()
            ->with(sprintf('repositories.generator.%s_suffix', $type))
            ->andReturn($this->suffix);
    }

    /**
     * @param array       $variables
     * @param string|null $implementation
     */
    private function setupReplacer(array $variables, string $implementation = null)
    {
        $fileName = null === $implementation ? $this->fileName : $this->fileNameWithImplementation;

        $this->variablesReplacer
            ->shouldReceive($this->method([SimpleVariableReplacer::class, 'replace']))
            ->once()
            ->with($fileName, $variables)
            ->andReturn($this->getReplacedFilename($variables, $implementation));
    }

    /**
     * @param array       $variables
     * @param string|null $implementation
     *
     * @return string
     */
    private function getReplacedFilename(array $variables, string $implementation = null): string
    {
        $fileName = null === $implementation ? $this->fileName : $this->fileNameWithImplementation;

        $str = str_replace(
            array_map(function ($item) {
                return '{' . $item . '}';
            }, array_keys($variables)),
            array_values($variables),
            $fileName
        );

        return str_replace(['\\\\', '//', '\\', '/'], DIRECTORY_SEPARATOR, $str);
    }
}
