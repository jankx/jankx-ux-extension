<?php
namespace Jankx\Extensions\JankxUX\Builder\Core\Collections;

/**
 * Templates Collection
 * Manages pre-built templates and layouts
 */
class Templates implements \Countable, \IteratorAggregate
{
    protected $templates = [];

    /**
     * Register a template
     */
    public function register($id, $config = [])
    {
        $defaults = [
            'id' => $id,
            'name' => $id,
            'title' => $id,
            'description' => '',
            'thumbnail' => '',
            'category' => 'general',
            'content' => '',
            'type' => 'layout', // layout, section, element
        ];

        $this->templates[$id] = array_merge($defaults, $config);
        
        return $this;
    }

    /**
     * Get a template
     */
    public function get($id)
    {
        return isset($this->templates[$id]) ? $this->templates[$id] : null;
    }

    /**
     * Check if template exists
     */
    public function has($id)
    {
        return isset($this->templates[$id]);
    }

    /**
     * Remove a template
     */
    public function remove($id)
    {
        unset($this->templates[$id]);
        return $this;
    }

    /**
     * Get all templates
     */
    public function all()
    {
        return $this->templates;
    }

    /**
     * Get templates by category
     */
    public function byCategory($category = null)
    {
        if ($category === null) {
            $result = [];
            foreach ($this->templates as $id => $template) {
                $cat = $template['category'] ?: 'general';
                $result[$cat][$id] = $template;
            }
            return $result;
        }

        return array_filter($this->templates, function($tpl) use ($category) {
            return $tpl['category'] === $category;
        });
    }

    /**
     * Get templates by type
     */
    public function byType($type)
    {
        return array_filter($this->templates, function($tpl) use ($type) {
            return $tpl['type'] === $type;
        });
    }

    /**
     * Search templates
     */
    public function search($query)
    {
        $query = strtolower($query);
        return array_filter($this->templates, function($tpl) use ($query) {
            return stripos(strtolower($tpl['name']), $query) !== false ||
                   stripos(strtolower($tpl['title']), $query) !== false ||
                   stripos(strtolower($tpl['description']), $query) !== false;
        });
    }

    /**
     * Load template from file
     */
    public function loadFromFile($id, $filepath, $config = [])
    {
        if (file_exists($filepath)) {
            $content = file_get_contents($filepath);
            $this->register($id, array_merge($config, ['content' => $content]));
        }
        
        return $this;
    }

    /**
     * Count templates
     */
    public function count(): int
    {
        return count($this->templates);
    }

    /**
     * Get iterator
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->templates);
    }
}
