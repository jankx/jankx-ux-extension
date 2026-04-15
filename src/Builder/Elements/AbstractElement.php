<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

use Jankx\Extensions\JankxUX\Builder\ElementRegistry;

/**
 * Abstract Element - Base class for all builder elements
 * PSR-4 compliant with static registration
 */
abstract class AbstractElement
{
    protected static $tag = '';
    protected static $config = [];

    /**
     * Get element configuration
     */
    abstract protected static function getConfig();

    /**
     * Get element tag
     */
    protected static function getTag()
    {
        if (empty(static::$tag)) {
            // Auto-generate tag from class name
            $class = new \ReflectionClass(static::class);
            return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $class->getShortName()));
        }
        return static::$tag;
    }

    /**
     * Register this element
     */
    public static function register()
    {
        $config = static::getConfig();
        $tag = static::getTag();
        
        ElementRegistry::register($tag, $config);
    }

    /**
     * Render element template
     * WordPress shortcode compatible: ($atts, $content)
     */
    abstract public static function render($atts = [], $content = '');

    /**
     * Parse WordPress atts to element options
     */
    protected static function parseAtts($atts, $defaults = [])
    {
        return shortcode_atts($defaults, $atts);
    }
}
