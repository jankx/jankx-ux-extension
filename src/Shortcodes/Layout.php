<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

class Layout
{
    public static function init()
    {
        add_shortcode('row', [self::class, 'renderRow']);
        add_shortcode('col', [self::class, 'renderCol']);
        add_shortcode('ux_block', [self::class, 'renderBlock']);
        add_shortcode('block', [self::class, 'renderBlock']);
    }

    public static function renderRow($atts, $content = null)
    {
        $atts = shortcode_atts(['class' => ''], $atts);
        return sprintf('<div class="row %s">%s</div>', esc_attr($atts['class']), do_shortcode($content));
    }

    public static function renderCol($atts, $content = null)
    {
        $atts = shortcode_atts(['span' => '12', 'class' => ''], $atts);
        return sprintf('<div class="col large-%s %s"><div class="col-inner">%s</div></div>', 
            esc_attr($atts['span']), 
            esc_attr($atts['class']), 
            do_shortcode($content)
        );
    }

    public static function renderBlock($atts, $content = null)
    {
        $atts = shortcode_atts(['id' => ''], $atts);
        if (empty($atts['id'])) return '';

        $query = new \WP_Query([
            'post_type' => 'ux_block',
            'name' => $atts['id'],
            'posts_per_page' => 1
        ]);

        if (!$query->have_posts()) return '';

        return sprintf(
            '<div class="jux-block-container" data-block-id="%s">%s</div>',
            esc_attr($atts['id']),
            do_shortcode($query->posts[0]->post_content)
        );
    }
}
