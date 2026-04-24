<?php
namespace Jankx\Extensions\JankxUX\Builder;

/**
 * Element Registry - Static class for registering builder elements
 * PSR-4 compliant, no global functions
 */
class ElementRegistry
{
    protected static $elements = [];
    protected static $initialized = false;

    /**
     * Register an element
     */
    public static function register($tag, $config = [])
    {
        $defaults = [
            'type' => 'element',
            'name' => $tag,
            'title' => $tag,
            'category' => 'General',
            'description' => '',
            'thumbnail' => '',
            'options' => [],
            'presets' => [],
            'allow_in' => [],
            'template' => null,
            'wrap' => false,
        ];

        self::$elements[$tag] = array_merge($defaults, $config);
        return true;
    }

    /**
     * Get all elements
     */
    public static function all()
    {
        return self::$elements;
    }

    /**
     * Get element by tag
     */
    public static function get($tag)
    {
        return self::$elements[$tag] ?? null;
    }

    /**
     * Check if element exists
     */
    public static function has($tag)
    {
        return isset(self::$elements[$tag]);
    }

    /**
     * Get elements by category
     */
    public static function byCategory()
    {
        $categorized = [];
        foreach (self::$elements as $tag => $element) {
            $cat = $element['category'] ?: 'General';
            $categorized[$cat][$tag] = $element;
        }
        return $categorized;
    }

    /**
     * Alias for byCategory() used in tests
     */
    public static function getCategorizedElements()
    {
        return self::byCategory();
    }

    /**
     * Search elements by name or tag
     */
    public static function search($query)
    {
        $query = strtolower($query);
        return array_filter(self::$elements, function($el, $tag) use ($query) {
            return strpos(strtolower($tag), $query) !== false || 
                   strpos(strtolower($el['name'] ?? ''), $query) !== false;
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Get elements by specific category
     */
    public static function getByCategory($category)
    {
        return array_filter(self::$elements, function($el) use ($category) {
            return ($el['category'] ?? 'General') === $category;
        });
    }


    /**
     * Remove element
     */
    public static function remove($tag)
    {
        unset(self::$elements[$tag]);
    }

    /**
     * Clear all elements
     */
    public static function clear()
    {
        self::$elements = [];
    }

    /**
     * Get element count
     */
    public static function count()
    {
        return count(self::$elements);
    }

    /**
     * Initialize and load all element classes
     */
    public static function init()
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        // Load all element classes from PSR-4 directory
        self::loadElementClasses();
    }

    /**
     * Load element classes using PSR-4 autoloading
     */
    protected static function loadElementClasses()
    {
        $elementsDir = __DIR__ . '/Elements';

        if (!is_dir($elementsDir)) {
            error_log('JUX Builder: Elements directory not found: ' . $elementsDir);
            return;
        }

        // Scan for element classes
        $files = glob($elementsDir . '/*.php');

        foreach ($files as $file) {
            $className = basename($file, '.php');

            // Skip AbstractElement
            if ($className === 'AbstractElement') {
                continue;
            }

            $fqcn = 'Jankx\\Extensions\\JankxUX\\Builder\\Elements\\' . $className;

            // Require the file first
            require_once $file;

            if (class_exists($fqcn)) {
                // Call static register method if exists
                if (method_exists($fqcn, 'register')) {
                    $fqcn::register();
                    error_log('JUX Builder: Registered element: ' . $className);
                }
            } else {
                error_log('JUX Builder: Class not found: ' . $fqcn);
            }
        }

        error_log('JUX Builder: Total elements registered: ' . self::count());
    }
}
