<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

class Section extends AbstractShortcode
{
    public static function render($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'bg'        => '',
            'bg_color'  => '',
            'padding'   => '30px',
            'height'    => '', // auto, 500px, etc
            'dark'      => 'false',
            'class'     => '',
        ), $atts);

        $style = [];
        if ($atts['bg']) $style[] = 'background-image: url(' . esc_url($atts['bg']) . ')';
        if ($atts['bg_color']) $style[] = 'background-color: ' . esc_attr($atts['bg_color']);
        if ($atts['padding']) $style[] = 'padding: ' . esc_attr($atts['padding']) . ' 0';
        if ($atts['height']) $style[] = 'min-height: ' . esc_attr($atts['height']);

        $classes = ['section'];
        if ($atts['dark'] === 'true') $classes[] = 'dark';
        if ($atts['class']) $classes[] = $atts['class'];

        return sprintf(
            '<section class="%s" style="%s"><div class="section-content relative">%s</div></section>',
            esc_attr(implode(' ', $classes)),
            esc_attr(implode(';', $style)),
            do_shortcode($content)
        );
    }
}
