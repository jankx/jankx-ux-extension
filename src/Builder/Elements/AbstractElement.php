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
     * Get element configuration.
     * Override in child class.
     */
    public static function getConfig()
    {
        return [];
    }

    /**
     * Get element shortcode tag.
     * Override in child class or set $tag property.
     */
    public static function getTag(): string
    {
        if (!empty(static::$tag)) {
            return static::$tag;
        }
        // Auto-generate tag from class name (PascalCase → snake_case)
        $class = new \ReflectionClass(static::class);
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $class->getShortName()));
    }

    /**
     * Register this element with ElementRegistry.
     */
    public static function register()
    {
        $config = static::getConfig();
        $tag    = static::getTag();

        ElementRegistry::register($tag, $config);
    }

    /**
     * Render element - WordPress shortcode compatible: ($atts, $content)
     * Must be implemented in all child classes.
     */
    abstract public static function render($atts = [], $content = '');

    /**
     * Render placeholder for empty containers in builder mode
     */
    public static function renderPlaceholder($tag, $name = '')
    {
        if (!defined('JUX_BUILDER')) return '';
        $name = $name ?: ucfirst($tag);
        return sprintf(
            '<div class="jux-placeholder jux-placeholder-%s" data-tag="%s">
                <div class="jux-placeholder-inner">
                    <button class="jux-placeholder-add-btn">
                        <span class="dashicons dashicons-plus"></span>
                        %s
                    </button>
                </div>
            </div>',
            esc_attr($tag),
            esc_attr($tag),
            __('Add elements', 'jankx')
        );
    }
}
