<?php
namespace Jankx\Extensions\JankxUX\Builder\Core\Collections;

/**
 * Elements Collection
 * Manages all builder elements/shortcodes
 */
class Elements implements \Countable, \IteratorAggregate
{
    protected $elements = [];

    /**
     * Register an element
     */
    public function register($tag, $config = [])
    {
        $defaults = [
            'type' => 'element',
            'name' => $tag,
            'title' => $tag,
            'category' => __('General', 'jankx'),
            'description' => '',
            'thumbnail' => '',
            'options' => [],
            'template' => null,
            'wrap' => false,
            'presets' => [],
            'allow_in' => [],
            'requires' => [],
        ];

        $this->elements[$tag] = array_merge($defaults, $config);
        
        return $this;
    }

    /**
     * Get an element by tag
     */
    public function get($tag)
    {
        return isset($this->elements[$tag]) ? $this->elements[$tag] : null;
    }

    /**
     * Check if element exists
     */
    public function has($tag)
    {
        return isset($this->elements[$tag]);
    }

    /**
     * Remove an element
     */
    public function remove($tag)
    {
        unset($this->elements[$tag]);
        return $this;
    }

    /**
     * Get all elements
     */
    public function all()
    {
        return $this->elements;
    }

    /**
     * Get elements by category
     */
    public function byCategory($category = null)
    {
        if ($category === null) {
            $result = [];
            foreach ($this->elements as $tag => $element) {
                $cat = $element['category'] ?: __('General', 'jankx');
                $result[$cat][$tag] = $element;
            }
            return $result;
        }

        return array_filter($this->elements, function($el) use ($category) {
            return $el['category'] === $category;
        });
    }

    /**
     * Get element categories
     */
    public function categories()
    {
        $categories = [];
        foreach ($this->elements as $element) {
            $cat = $element['category'] ?: __('General', 'jankx');
            if (!in_array($cat, $categories)) {
                $categories[] = $cat;
            }
        }
        return $categories;
    }

    /**
     * Search elements
     */
    public function search($query)
    {
        $query = strtolower($query);
        return array_filter($this->elements, function($el) use ($query) {
            return stripos(strtolower($el['name']), $query) !== false ||
                   stripos(strtolower($el['title']), $query) !== false ||
                   stripos(strtolower($el['description']), $query) !== false;
        });
    }

    /**
     * Count elements
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * Get iterator
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * Merge with another collection
     */
    public function merge(Elements $collection)
    {
        $this->elements = array_merge($this->elements, $collection->all());
        return $this;
    }
}
