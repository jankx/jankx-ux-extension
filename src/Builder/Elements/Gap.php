<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Gap Element - [gap] shortcode
 * Full parity with Flatsome's flatsome_gap_shortcode() function
 */
class Gap extends AbstractElement
{
    protected static $tag = 'gap';

    protected static function getConfig()
    {
        return [
            'type'        => 'element',
            'name'        => 'Gap',
            'title'       => __('Gap', 'jankx'),
            'category'    => __('Layout', 'jankx'),
            'description' => __('Add vertical spacing between elements.', 'jankx'),
            'wrap'        => false,
            'options'     => [],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $atts = shortcode_atts([
            '_id'        => 'gap-' . rand(),
            'height'     => '30px',
            'height__sm' => '',
            'height__md' => '',
            'class'      => '',
            'visibility' => '',
        ], $atts);

        extract($atts);

        $classes = ['gap-element', 'clearfix'];
        if ($class)      $classes[] = $class;
        if ($visibility) $classes[] = $visibility;

        $class_str = implode(' ', $classes);

        // Build pad-top style
        $style = 'display:block; height:auto; padding-top:' . esc_attr($height) . ';';

        // Responsive overrides via <style>
        $responsive_css = '';
        if ($height__md) {
            $responsive_css .= '@media (max-width:960px) { #' . esc_attr($_id) . ' { padding-top:' . esc_attr($height__md) . '!important; } }';
        }
        if ($height__sm) {
            $responsive_css .= '@media (max-width:640px) { #' . esc_attr($_id) . ' { padding-top:' . esc_attr($height__sm) . '!important; } }';
        }

        $output = '<div id="' . esc_attr($_id) . '" class="' . esc_attr($class_str) . '" style="' . $style . '"></div>';
        if ($responsive_css) {
            $output .= '<style>' . $responsive_css . '</style>';
        }

        return $output;
    }
}
