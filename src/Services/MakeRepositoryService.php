<?php

namespace Davesweb\Repositories\Services;

use Davesweb\Repositories\Exceptions\FileAlreadyExists;
use Davesweb\Repositories\ValueObjects\CreatedClass;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRepositoryService
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var SimpleVariableReplacer
     */
    private $replacer;

    /**
     * @var StubService
     */
    private $stubService;

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * @var FileNameService
     */
    private $fileNameService;

    /**
     * @var Kernel
     */
    private $console;

    /**
     * @param Repository             $config
     * @param NullOutput             $nullOutput
     * @param SimpleVariableReplacer $replacer
     * @param StubService            $stubService
     * @param Filesystem             $files
     * @param FileNameService        $fileNameService
     * @param Kernel                 $console
     */
    public function __construct(
        Repository $config,
        NullOutput $nullOutput,
        SimpleVariableReplacer $replacer,
        StubService $stubService,
        Filesystem $files,
        FileNameService $fileNameService,
        Kernel $console
    ) {
        $this->config          = $config;
        $this->output          = $nullOutput;
        $this->replacer        = $replacer;
        $this->stubService     = $stubService;
        $this->files           = $files;
        $this->fileNameService = $fileNameService;
        $this->console         = $console;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param string $name
     * @param string $namespace
     * @param array  $implementations
     * @param bool   $createEntity
     * @param bool   $createMigration
     */
    public function make(
        string $name,
        string $namespace,
        array $implementations,
        bool $createEntity,
        bool $createMigration
    ) {
        if ($createEntity) {
            $entityInterface = $this->makeEntityInterface($name, $namespace);
            $entity          = $this->makeEntity($name, $namespace, $entityInterface);
        } else {
            $entityInterface = new CreatedClass('Model', 'Illuminate\\Database\\Eloquent');
            $entity          = new CreatedClass('Model', 'Illuminate\\Database\\Eloquent');
        }

        $interface = $this->makeInterface($name, $namespace, $entityInterface);

        $available = $this->config->get('repositories.generator.available_implementations', []);
        $concrete  = [];
        if (!empty($implementations)) {
            $concrete = array_intersect_key($implementations, array_flip($available));
        }

        $created = [];
        foreach ($concrete ?: $available as $implementation => $baseClass) {
            $created[] = $this->makeRepository($name, $namespace, ucfirst($implementation), $baseClass, $interface, $entity);
        }

        if ($createMigration) {
            $this->makeMigration($name);
        }

        $this->write('----------------');
        $this->write('Done');
    }

    /**
     * @param string $name
     * @param string $namespace
     *
     * @return CreatedClass
     */
    private function makeEntityInterface(string $name, string $namespace): CreatedClass
    {
        $this->write('Creating entity interface for %s', $name);

        $fullNamespace = $this->normalizeAndReplace(
            $this->config->get('repositories.generator.entity_interface_namespace'),
            ['namespace' => $namespace]
        );

        $fullName =
            $this->config->get('repositories.generator.entity_interface_prefix') .
            $name .
            $this->config->get('repositories.generator.entity_interface_suffix')
        ;

        $variables = [
            'namespace' => rtrim($fullNamespace, '\\'),
            'name'      => $fullName,
        ];

        $contents = $this->normalizeAndReplace(
            $this->stubService->getStubContent('entity_interface'),
            $variables
        );

        $filename = $this->fileNameService->getFilename('entity_interface', $name, $namespace);
        if ($this->files->exists($filename)) {
            throw new FileAlreadyExists($filename);
        }

        $this->makeFile($filename, $contents);

        $interface = new CreatedClass($fullName, $fullNamespace);

        return $interface;
    }

    /**
     * @param string       $name
     * @param string       $namespace
     * @param CreatedClass $interface
     *
     * @return CreatedClass
     */
    private function makeEntity(string $name, string $namespace, CreatedClass $interface): CreatedClass
    {
        $this->write('Creating entity class for %s', $name);

        $fullNamespace = $this->normalizeAndReplace(
            $this->config->get('repositories.generator.entity_namespace'),
            ['namespace' => $namespace]
        );

        $fullName =
            $this->config->get('repositories.generator.entity_prefix') .
            $name .
            $this->config->get('repositories.generator.entity_suffix')
        ;

        $variables = [
            'namespace' => rtrim($fullNamespace, '\\'),
            'interface' => $interface->getFullClassName(),
            'name'      => $fullName,
        ];

        $contents = $this->normalizeAndReplace(
            $this->stubService->getStubContent('entity'),
            $variables
        );

        $filename = $this->fileNameService->getFilename('entity', $name, $namespace);
        if ($this->files->exists($filename)) {
            throw new FileAlreadyExists($filename);
        }

        $this->makeFile($filename, $contents);

        $entity = new CreatedClass($fullName, $fullNamespace);

        return $entity;
    }

    /**
     * @param string       $name
     * @param string       $namespace
     * @param CreatedClass $entity
     *
     * @return CreatedClass
     */
    private function makeInterface(string $name, string $namespace, CreatedClass $entity): CreatedClass
    {
        $this->write('Creating repository interface class for %s', $name);

        $fullNamespace = $this->normalizeAndReplace(
            $this->config->get('repositories.generator.interface_namespace'),
            ['namespace' => $namespace]
        );

        $fullName =
            $this->config->get('repositories.generator.interface_prefix') .
            $name .
            $this->config->get('repositories.generator.interface_suffix')
        ;

        $variables = [
            'namespace'        => rtrim($fullNamespace, '\\'),
            'entity_interface' => $entity->getFullClassName(),
            'name'             => $fullName,
        ];

        $contents = $this->normalizeAndReplace(
            $this->stubService->getStubContent('interface'),
            $variables
        );

        $filename = $this->fileNameService->getFilename('interface', $name, $namespace);
        if ($this->files->exists($filename)) {
            throw new FileAlreadyExists($filename);
        }

        $this->makeFile($filename, $contents);

        $interface = new CreatedClass($fullName, $fullNamespace);

        return $interface;
    }

    /**
     * @param string       $name
     * @param string       $namespace
     * @param string       $implementation
     * @param string       $baseClass
     * @param CreatedClass $interface
     * @param CreatedClass $entity
     *
     * @return CreatedClass
     */
    private function makeRepository(
        string $name,
        string $namespace,
        string $implementation,
        string $baseClass,
        CreatedClass $interface,
        CreatedClass $entity
    ): CreatedClass {
        $this->write('Creating repository class for %s with implementation %s', $name, $implementation);

        $fullNamespace = $this->normalizeAndReplace(
            $this->config->get('repositories.generator.repository_namespace'),
            ['namespace' => $namespace, 'implementation' => $implementation]
        );

        $fullName =
            $this->config->get('repositories.generator.repository_prefix') .
            $name .
            $this->config->get('repositories.generator.repository_suffix')
        ;

        $variables = [
            'namespace' => rtrim($fullNamespace, '\\'),
            'interface' => $interface->getFullClassName(),
            'entity'    => $entity->getFullClassName(),
            'concrete'  => $baseClass,
            'name'      => $fullName,
        ];

        $contents = $this->normalizeAndReplace(
            $this->stubService->getStubContent('repository'),
            $variables
        );

        $filename = $this->fileNameService->getFilename('repository', $name, $namespace, $implementation);
        if ($this->files->exists($filename)) {
            throw new FileAlreadyExists($filename);
        }

        $this->makeFile($filename, $contents);

        $repository = new CreatedClass($fullName, $fullNamespace);

        return $repository;
    }

    /**
     * @param string $name
     *
     * @return CreatedClass
     */
    private function makeMigration(string $name): CreatedClass
    {
        $this->write('Creating migration for %s', $name);

        // Pluralize the name so it's consistent with Laravel
        $name = Str::plural($name);

        $name = 'Create' . $name . 'Table';

        $this->console->call('make:migration', ['name' => $name]);

        return new CreatedClass($name, null);
    }

    /**
     * @param string  $text
     * @param mixed[] $options
     */
    private function write(string $text, ...$options)
    {
        $this->output->writeln(__(vsprintf($text, $options)));
    }

    /**
     * @param string $string
     * @param array  $variables
     *
     * @return string
     */
    private function normalizeAndReplace(string $string, array $variables = []): string
    {
        $string = $this->replacer->replace($string, $variables);

        return str_replace(['//', '\\\\'], ['/', '\\'], $string);
    }

    /**
     * @param string $filename
     * @param string $contents
     */
    private function makeFile(string $filename, string $contents)
    {
        $pathInfo = pathinfo($filename);

        if (!$this->files->isDirectory($pathInfo['dirname'])) {
            $this->files->makeDirectory($pathInfo['dirname'], 0755, true);
        }

        $this->files->put($filename, $contents);
    }
}
