<?php
namespace Jankx\Extensions\JankxUX\Builder;

class BuilderManager
{
    protected static $elements = [];

    public static function registerShortcode($tag, $args)
    {
        $defaults = array(
            'type' => 'element',
            'name' => $tag,
            'category' => __('General', 'jankx'),
            'options' => array(),
        );
        
        self::$elements[$tag] = array_merge($defaults, $args);
    }

    public static function getElements()
    {
        return self::$elements;
    }

    public static function getCategorizedElements()
    {
        $categorized = [];
        foreach (self::$elements as $tag => $el) {
            $cat = isset($el['category']) ? strtoupper($el['category']) : strtoupper(__('General', 'jankx'));
            $categorized[$cat][$tag] = $el;
        }
        return $categorized;
    }

    public static function init()
    {
        add_action('init', [self::class, 'loadElements'], 20);
    }

    public static function loadElements()
    {
        // Dynamically load shortcode builder registrations
        // Note: For PSR-4, we might want to register them explicitly or scan
        // but for Element Parity, scanning the directory is often easiest.
        $path = dirname(dirname(__DIR__)) . '/inc/builder/shortcodes';
        if (is_dir($path)) {
            foreach (glob($path . '/*.php') as $file) {
                require_once $file;
            }
        }
    }
}
