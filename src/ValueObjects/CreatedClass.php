<?php

namespace Davesweb\Repositories\ValueObjects;

class CreatedClass
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param string      $name
     * @param string|null $namespace
     */
    public function __construct(string $name, string $namespace = null)
    {
        $this->name      = $name;
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getFullClassName(): string
    {
        return rtrim($this->getNamespace(), '\\') . '\\' . $this->getName();
    }
}
