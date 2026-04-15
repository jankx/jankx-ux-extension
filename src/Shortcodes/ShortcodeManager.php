<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

use Jankx\Extensions\JankxUX\Builder\Elements;
use Jankx\Extensions\JankxUX\Builder\ElementRegistry;
use Jankx\Extensions\JankxUX\Shortcodes\WooCommerce;

/**
 * Shortcode Manager - Registers all Flatsome-compatible shortcodes
 * PSR-4 with Builder Elements
 */
class ShortcodeManager
{
    /**
     * Map of shortcode tags to Element classes
     */
    protected static $elementMap = [
        'row'       => Elements\Row::class,
        'col'       => Elements\Column::class,
        'section'   => Elements\Section::class,
        'text'      => Elements\Text::class,
        'button'    => Elements\Button::class,
        'ux_slider' => Elements\Slider::class,
    ];

    public static function init()
    {
        // Register WordPress shortcodes from Element classes
        foreach (self::$elementMap as $tag => $class) {
            add_shortcode($tag, [$class, 'render']);
        }

        // Register additional shortcodes
        self::registerLegacyShortcodes();

        // Initialize WooCommerce shortcodes if available
        if (class_exists('WooCommerce')) {
            WooCommerce::init();
        }
    }

    /**
     * Register legacy/alias shortcodes for full Flatsome compatibility
     */
    protected static function registerLegacyShortcodes()
    {
        // Aliases for Flatsome compatibility
        $aliases = [
            'ux_section' => Elements\Section::class,
        ];

        foreach ($aliases as $tag => $class) {
            add_shortcode($tag, [$class, 'render']);
        }
    }

    /**
     * Render shortcode by tag (used by builder)
     */
    public static function render($tag, $atts = [], $content = '')
    {
        if (isset(self::$elementMap[$tag])) {
            $class = self::$elementMap[$tag];
            return $class::render($atts, $content);
        }

        return $content;
    }

    /**
     * Get all supported shortcode tags
     */
    public static function getTags()
    {
        return array_keys(self::$elementMap);
    }
}
