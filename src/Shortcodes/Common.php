<?php
namespace Jankx\Extensions\JankxUX\Shortcodes;

class Common extends AbstractShortcode
{
    public static function renderGap($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'height' => '30px',
        ), $atts);

        return sprintf('<div class="gap-element clearfix" style="display:block; height:auto; padding-top:%s;"></div>', esc_attr($atts['height']));
    }

    public static function renderDivider($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'width'  => 'small', // small, full
            'height' => '2px',
            'margin' => '20px',
            'align'  => 'left',
            'color'  => '',
        ), $atts);

        $style = [
            'height: ' . esc_attr($atts['height']),
            'margin-top: ' . esc_attr($atts['margin']),
            'margin-bottom: ' . esc_attr($atts['margin']),
        ];

        if ($atts['color']) $style[] = 'background-color: ' . esc_attr($atts['color']);

        $classes = ['is-divider', 'divider-' . $atts['width'], 'align-' . $atts['align']];

        return sprintf('<div class="%s" style="%s"></div>', esc_attr(implode(' ', $classes)), esc_attr(implode(';', $style)));
    }

    public static function render($atts, $content = null) {}
}
