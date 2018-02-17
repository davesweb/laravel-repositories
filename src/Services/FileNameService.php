<?php

namespace Davesweb\Repositories\Services;

use Illuminate\Contracts\Config\Repository;

class FileNameService
{
    /**
     * @var Repository
     */
    private $config;

    /**
     * @var SimpleVariableReplacer
     */
    private $replacer;

    /**
     * @param Repository             $config
     * @param SimpleVariableReplacer $replacer
     */
    public function __construct(Repository $config, SimpleVariableReplacer $replacer)
    {
        $this->config   = $config;
        $this->replacer = $replacer;
    }

    /**
     * @param string      $type
     * @param string      $name
     * @param string      $namespace
     * @param string|null $implementation
     *
     * @return string
     */
    public function getFilename(string $type, string $name, string $namespace, string $implementation = null): string
    {
        $filename = $this->config->get(sprintf('repositories.generator.%s_path', $type));

        $prefix = $this->config->get(sprintf('repositories.generator.%s_prefix', $type));
        $suffix = $this->config->get(sprintf('repositories.generator.%s_suffix', $type));

        $variables = [
            'name'      => $prefix . $name . $suffix,
            'namespace' => $namespace,
        ];

        if (null !== $implementation) {
            $variables['implementation'] = $implementation;
        }

        return $this->normalizeAndReplace($filename, $variables);
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

        return str_replace(['\\\\', '//', '\\', '/'], DIRECTORY_SEPARATOR, $string);
    }
}
