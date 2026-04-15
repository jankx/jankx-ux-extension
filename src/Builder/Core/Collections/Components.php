<?php
namespace Jankx\Extensions\JankxUX\Builder\Core\Collections;

/**
 * Components Collection
 * Manages UI components for builder interface
 */
class Components implements \Countable, \IteratorAggregate
{
    protected $components = [];

    /**
     * Register a component
     */
    public function register($name, $config = [])
    {
        $defaults = [
            'name' => $name,
            'template' => null,
            'data' => [],
            'scripts' => [],
            'styles' => [],
        ];

        $this->components[$name] = array_merge($defaults, $config);
        
        return $this;
    }

    /**
     * Get a component
     */
    public function get($name)
    {
        return isset($this->components[$name]) ? $this->components[$name] : null;
    }

    /**
     * Check if component exists
     */
    public function has($name)
    {
        return isset($this->components[$name]);
    }

    /**
     * Remove a component
     */
    public function remove($name)
    {
        unset($this->components[$name]);
        return $this;
    }

    /**
     * Get all components
     */
    public function all()
    {
        return $this->components;
    }

    /**
     * Render a component
     */
    public function render($name, $data = [])
    {
        $component = $this->get($name);
        
        if (!$component || !$component['template']) {
            return '';
        }

        $template = $component['template'];
        $data = array_merge($component['data'], $data);

        if (is_callable($template)) {
            return call_user_func($template, $data);
        }

        if (file_exists($template)) {
            extract($data);
            ob_start();
            include $template;
            return ob_get_clean();
        }

        return '';
    }

    /**
     * Count components
     */
    public function count(): int
    {
        return count($this->components);
    }

    /**
     * Get iterator
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->components);
    }
}
