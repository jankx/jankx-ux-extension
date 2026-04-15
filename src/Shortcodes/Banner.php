<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

class Banner extends AbstractShortcode
{
    public static function render($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'bg'        => '',
            'height'    => '500px',
            'bg_overlay' => '',
            'hover'      => '',
            'class'      => '',
        ), $atts);

        $style = [];
        if ($atts['bg']) $style[] = 'background-image: url(' . esc_url($atts['bg']) . ')';
        if ($atts['height']) $style[] = 'height: ' . esc_attr($atts['height']);

        return sprintf(
            '<div class="banner %s" style="%s">
                <div class="banner-inner fill">
                    <div class="banner-bg fill" style="%s"></div>
                    <div class="banner-layers container">%s</div>
                </div>
            </div>',
            esc_attr($atts['class']),
            esc_attr($atts['height'] ? 'height:'.$atts['height'] : ''),
            esc_attr(implode(';', $style)),
            do_shortcode($content)
        );
    }
}
