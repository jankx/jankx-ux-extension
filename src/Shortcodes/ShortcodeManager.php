<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

use Jankx\Extensions\JankxUX\Builder\Elements;
use Jankx\Extensions\JankxUX\Builder\ElementRegistry;
use Jankx\Extensions\JankxUX\Shortcodes\WooCommerce;

/**
 * Shortcode Manager - Registers all Flatsome-compatible shortcodes
 * Full parity with Flatsome shortcode set
 */
class ShortcodeManager
{
    /**
     * Map of shortcode tags to Element classes
     * Matches Flatsome's add_shortcode() registrations exactly
     */
    protected static $elementMap = [
        // Layout
        'row'            => Elements\Row::class,
        'row_inner'      => Elements\Row::class,
        'row_inner_1'    => Elements\Row::class,
        'row_inner_2'    => Elements\Row::class,
        'col'            => Elements\Column::class,
        'col_inner'      => Elements\Column::class,
        'col_inner_1'    => Elements\Column::class,
        'col_inner_2'    => Elements\Column::class,
        'section'        => Elements\Section::class,
        'section_inner'  => Elements\Section::class,
        'ux_section'     => Elements\Section::class,
        'background'     => Elements\Section::class,

        // Content builders
        'ux_banner'      => Elements\Banner::class,
        'text_box'       => Elements\TextBox::class,
        'text'           => Elements\Text::class,
        'button'         => Elements\Button::class,
        'gap'            => Elements\Gap::class,
        'ux_slider'      => Elements\Slider::class,
        'ux_image'       => Elements\Image::class,
    ];

    public static function init()
    {
        // Register WordPress shortcodes from Element classes
        foreach (self::$elementMap as $tag => $class) {
            add_shortcode($tag, [$class, 'render']);
        }

        // Load additional shortcodes (ux_html, ux_video, divider, title, tab, etc.)
        require_once __DIR__ . '/AdditionalShortcodes.php';

        // Register additional standalone shortcodes
        self::registerStandaloneShortcodes();

        // Initialize WooCommerce shortcodes if available
        if (class_exists('WooCommerce')) {
            WooCommerce::init();
        }
    }

    /**
     * Register shortcodes that are implemented as standalone functions
     * (not yet migrated to PSR-4 Element classes)
     */
    protected static function registerStandaloneShortcodes()
    {
        // video_button - Flatsome alias
        if (!shortcode_exists('video_button')) {
            add_shortcode('video_button', [__CLASS__, 'renderVideoButton']);
        }
    }

    /**
     * Simple video button shortcode [video_button]
     */
    public static function renderVideoButton($atts, $content = '')
    {
        $atts = shortcode_atts([
            'video' => '',
            'size'  => '',
        ], $atts);

        $wrapper_style = $atts['size'] ? ' style="font-size:' . esc_attr($atts['size']) . '%"' : '';
        $href          = $atts['video'] ? esc_url($atts['video']) : '#';

        return '<div class="video-button-wrapper"' . $wrapper_style . '>'
            . '<a href="' . $href . '" class="button open-video icon circle is-outline is-xlarge" role="button" aria-label="' . esc_attr__('Open video in lightbox', 'jankx') . '">'
            . '<i class="icon-play"></i>'
            . '</a>'
            . '</div>';
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
