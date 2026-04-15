<?php
namespace Jankx\Extensions\JankxUX\Builder;

/**
 * Builder Manager - PSR-4 Static Class
 * Uses ElementRegistry for PSR-4 element registration
 */
class BuilderManager
{
    protected static $initialized = false;

    public static function init()
    {
        if (self::$initialized) {
            return;
        }
        self::$initialized = true;

        // Initialize Element Registry with PSR-4 autoloading
        add_action('init', [ElementRegistry::class, 'init'], 10);
    }

    /**
     * Get all elements from registry
     */
    public static function getElements()
    {
        return ElementRegistry::all();
    }

    /**
     * Get categorized elements
     */
    public static function getCategorizedElements()
    {
        return ElementRegistry::byCategory();
    }

    /**
     * Register element (backward compatible)
     */
    public static function registerShortcode($tag, $config)
    {
        return ElementRegistry::register($tag, $config);
    }

    /**
     * Render element
     */
    public static function render($tag, $options, $content = '')
    {
        $element = ElementRegistry::get($tag);
        
        if (!$element || !is_callable($element['template'])) {
            return $content;
        }
        
        return call_user_func($element['template'], $options, $content);
    }
}
