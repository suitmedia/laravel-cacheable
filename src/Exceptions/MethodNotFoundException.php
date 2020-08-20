<?php

namespace Suitmedia\Cacheable\Exceptions;

use BadMethodCallException;

class MethodNotFoundException extends BadMethodCallException
{
    /**
     * Method name.
     *
     * @var string
     */
    protected $method;

    /**
     * Repository class name.
     *
     * @var string
     */
    protected $repository;

    /**
     * Get method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the repository name.
     *
     * @return string
     */
    public function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * Set Repository and method name then generate the
     * exception message.
     *
     * @param string $repository
     * @param string $method
     *
     * @return \Suitmedia\Cacheable\Exceptions\MethodNotFoundException
     */
    public function setRepositoryMethod($repository, $method): self
    {
        $this->repository = $repository;
        $this->method = $method;

        $this->message = "Method {$this->method}() doesn't exist in {$this->repository}";

        return $this;
    }
}
