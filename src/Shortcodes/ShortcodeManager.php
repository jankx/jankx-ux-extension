<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

use Jankx\Extensions\JankxUX\Shortcodes\WooCommerce;

class ShortcodeManager
{
    /**
     * List of all Flatsome shortcodes to support
     */
    protected static $shortcodes = array(
        'row'             => Layout::class . '::renderRow',
        'col'             => Layout::class . '::renderCol',
        'section'         => Section::class . '::render',
        'ux_slider'       => Slider::class . '::render',
        'ux_banner'       => Banner::class . '::render',
        'ux_banner_grid'  => BannerGrid::class . '::render',
        'ux_image'        => Image::class . '::render',
        'ux_text'         => Text::class . '::render',
        'button'          => Button::class . '::render',
        'ux_gallery'      => Gallery::class . '::render',
        'ux_video'        => Video::class . '::render',
        'ux_logo'         => Logo::class . '::render',
        'gap'             => Common::class . '::renderGap',
        'divider'         => Common::class . '::renderDivider',
        'block'           => Layout::class . '::renderBlock',
        'ux_block'        => Layout::class . '::renderBlock',
        'blog_posts'      => BlogPosts::class . '::render',
    );

    public static function init()
    {
        foreach (self::$shortcodes as $tag => $callback) {
            add_shortcode($tag, $callback);
        }

        // Initialize Specialized Shortcodes
        WooCommerce::init();
    }
}
