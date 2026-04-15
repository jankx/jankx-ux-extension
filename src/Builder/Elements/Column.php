<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Column Element - PSR-4 Static Class
 */
class Column extends AbstractElement
{
    protected static $tag = 'col';

    protected static function getConfig()
    {
        return [
            'type' => 'container',
            'name' => 'Column',
            'title' => __('Column', 'jankx'),
            'category' => __('Layout', 'jankx'),
            'description' => __('Create a column inside a row.', 'jankx'),
            'thumbnail' => '',
            'wrap' => true,
            'options' => [
                'span' => [
                    'type' => 'select',
                    'heading' => __('Column Width', 'jankx'),
                    'default' => '12',
                    'options' => [
                        '1' => '1/12', '2' => '2/12', '3' => '3/12',
                        '4' => '4/12', '5' => '5/12', '6' => '6/12',
                        '7' => '7/12', '8' => '8/12', '9' => '9/12',
                        '10' => '10/12', '11' => '11/12', '12' => '12/12',
                    ],
                ],
                'class' => [
                    'type' => 'text',
                    'heading' => __('Custom Class', 'jankx'),
                    'default' => '',
                ],
            ],
            'presets' => [
                'half' => [
                    'title' => __('1/2 Column', 'jankx'),
                    'options' => ['span' => '6'],
                ],
            ],
            'allow_in' => ['row'],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $options = shortcode_atts([
            '_id' => 'col-' . rand(),
            'span' => '12',
            'span__md' => '',
            'span__sm' => '',
            'class' => '',
            'visibility' => '',
            'animate' => '',
            'parallax' => '',
            'depth' => '',
            'depth_hover' => '',
            'padding' => '',
        ], $atts);

        // Stop if visibility is hidden
        if ($options['visibility'] === 'hidden') return '';

        $span = intval($options['span']);
        $classes = ['col', 'medium-' . $span];

        // Responsive classes
        if (!empty($options['span__md'])) $classes[] = 'small-' . intval($options['span__md']);
        if (!empty($options['span__sm'])) $classes[] = 'small-' . intval($options['span__sm']);

        // Animation
        if (!empty($options['animate'])) $classes[] = 'animated ' . esc_attr($options['animate']);

        // Parallax
        if (!empty($options['parallax'])) $classes[] = 'parallax';

        // Depth
        if (!empty($options['depth'])) $classes[] = 'box-shadow-' . intval($options['depth']);
        if (!empty($options['depth_hover'])) $classes[] = 'box-shadow-' . intval($options['depth_hover']) . '-hover';

        // Custom class & visibility
        if (!empty($options['class'])) $classes[] = esc_attr($options['class']);
        if (!empty($options['visibility'])) $classes[] = esc_attr($options['visibility']);

        $classString = implode(' ', $classes);
        $id = esc_attr($options['_id']);

        return '<div class="' . esc_attr($classString) . '" id="' . $id . '">'
            . '<div class="col-inner">'
            . do_shortcode($content)
            . '</div>'
            . '</div>';
    }
}
