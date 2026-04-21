<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * UX Stack Element - [ux_stack] shortcode
 */
class Stack extends AbstractElement
{
    protected static $tag = 'ux_stack';

    protected static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Stack',
            'title'       => __('Stack', 'jankx'),
            'category'    => __('Layout', 'jankx'),
            'description' => __('Flexbox container to stack elements.', 'jankx'),
            'wrap'        => true,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'              => 'stack-' . rand(),
            'direction'        => 'column', // row, column
            'distribute'       => 'start',  // start, center, end, between, around
            'align'            => 'center', // start, center, end, stretch
            'gap'              => '1',
            'class'            => '',
            'visibility'       => '',
            'tablet_direction' => '',
            'mobile_direction' => '',
        ], $atts);

        extract($atts);

        $classes = ['ux-stack', 'flex'];
        $classes[] = 'flex-' . $direction;
        if ($tablet_direction) $classes[] = 'md-flex-' . $tablet_direction;
        if ($mobile_direction) $classes[] = 'sm-flex-' . $mobile_direction;

        $classes[] = 'flex-justify-' . $distribute;
        $classes[] = 'flex-align-' . $align;
        $classes[] = 'flex-gap-' . $gap;

        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        return '<div id="' . esc_attr($_id) . '" class="' . esc_attr(implode(' ', $classes)) . '">' . do_shortcode($content) . '</div>';
    }
}
