<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * UX Hotspot - [ux_hotspot] shortcode
 */
class Hotspot extends AbstractElement
{
    protected static $tag = 'ux_hotspot';

    protected static function getConfig()
    {
        return [
            'type'        => 'element',
            'name'        => 'Hotspot',
            'title'       => __('Hotspot', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Add interactive points on an image.', 'jankx'),
            'wrap'        => false,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            'x'       => '50',
            'y'       => '50',
            'link'    => '',
            'icon'    => 'plus',
            'animate' => '',
            'color'   => 'primary',
            'bg'      => '',
        ], $atts);

        extract($atts);

        $style = "left: {$x}%; top: {$y}%;";
        if ($bg) $style .= " background-color: {$bg};";

        $classes = ['ux-hotspot', 'absolute'];
        if ($color) $classes[] = 'is-' . $color;

        return '<a href="' . esc_url($link) . '" class="' . esc_attr(implode(' ', $classes)) . '" style="' . esc_attr($style) . '"'
            . ($animate ? ' data-animate="' . esc_attr($animate) . '"' : '') . '>'
            . '<span class="hotspot-icon icon-' . esc_attr($icon) . '"><i class="icon-plus"></i></span>'
            . '</a>';
    }
}
