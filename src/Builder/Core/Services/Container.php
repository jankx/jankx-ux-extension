<?php
namespace Jankx\Extensions\JankxUX\Builder\Core\Services;

/**
 * Simple Dependency Injection Container
 * Mimics Flatsome's UxBuilder\Services\Container
 */
class Container
{
    protected $services = [];
    protected $factories = [];
    protected $instances = [];

    /**
     * Register a shared service (singleton)
     */
    public function service($name, callable $callback)
    {
        $this->services[$name] = $callback;
        return $this;
    }

    /**
     * Register a factory (new instance each time)
     */
    public function factory($name, callable $callback)
    {
        $this->factories[$name] = $callback;
        return $this;
    }

    /**
     * Resolve a service or factory
     */
    public function resolve($name = null)
    {
        if ($name === null) {
            return $this;
        }

        // Check for shared service
        if (isset($this->services[$name])) {
            if (!isset($this->instances[$name])) {
                $this->instances[$name] = $this->services[$name]($this);
            }
            return $this->instances[$name];
        }

        // Check for factory
        if (isset($this->factories[$name])) {
            return $this->factories[$name]($this);
        }

        throw new \Exception("Service '{$name}' not found in container");
    }

    /**
     * Create an instance of a class with dependencies
     */
    public function create($class, $args = [])
    {
        $reflection = new \ReflectionClass($class);
        
        if (empty($args)) {
            return $reflection->newInstance();
        }

        return $reflection->newInstanceArgs($args);
    }

    /**
     * Check if service exists
     */
    public function has($name)
    {
        return isset($this->services[$name]) || isset($this->factories[$name]);
    }
}
