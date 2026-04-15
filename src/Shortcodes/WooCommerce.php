<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

class WooCommerce extends AbstractShortcode
{
    public static function init()
    {
        if (!class_exists('WooCommerce')) {
            return;
        }

        add_shortcode('ux_products', [self::class, 'renderProducts']);
        add_shortcode('product_categories', [self::class, 'renderCategories']);
    }

    public static function renderProducts($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'columns' => '4',
            'limit'   => '8',
            'orderby' => 'date',
            'order'   => 'DESC',
            'type'    => 'slider', // slider, grid
        ), $atts);

        // Standard WooCommerce product loop shortcode bridge
        $shortcode = sprintf(
            '[products columns="%s" limit="%s" orderby="%s" order="%s"]',
            esc_attr($atts['columns']),
            esc_attr($atts['limit']),
            esc_attr($atts['orderby']),
            esc_attr($atts['order'])
        );

        if ($atts['type'] === 'slider') {
            return sprintf('<div class="ux-products-slider">%s</div>', do_shortcode($shortcode));
        }

        return do_shortcode($shortcode);
    }

    public static function renderCategories($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'columns' => '4',
            'number'  => '8',
        ), $atts);

        return do_shortcode(sprintf(
            '[product_categories columns="%s" number="%s"]',
            esc_attr($atts['columns']),
            esc_attr($atts['number'])
        ));
    }

    public static function render($atts, $content = null) {}
}
