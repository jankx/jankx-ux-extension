<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * UX Text Element - [ux_text] shortcode
 */
class UXText extends AbstractElement
{
    protected static $tag = 'ux_text';

    protected static function getConfig()
    {
        return [
            'type'        => 'container',
            'name'        => 'Advanced Text',
            'title'       => __('UX Text', 'jankx'),
            'category'    => __('Content', 'jankx'),
            'description' => __('Advanced text element with responsive font sizes.', 'jankx'),
            'wrap'        => true,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'         => 'ux-text-' . rand(),
            'font_size'   => '',
            'font_size__sm' => '',
            'font_size__md' => '',
            'line_height' => '',
            'text_align'  => '',
            'text_color'  => '',
            'class'       => '',
            'visibility'  => '',
        ], $atts);

        extract($atts);

        $classes = ['ux-text', 'last-reset'];
        if ($text_align) $classes[] = 'text-' . $text_align;
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        // Generate responsive CSS via our helper
        $style_tag = ux_builder_element_style_tag($_id, [
            'font_size'   => ['property' => 'font-size', 'unit' => 'px'],
            'line_height' => ['property' => 'line-height'],
            'text_color'  => ['property' => 'color'],
        ], $atts);

        return $style_tag . '<div id="' . esc_attr($_id) . '" class="' . esc_attr(implode(' ', $classes)) . '">' . do_shortcode($content) . '</div>';
    }
}
