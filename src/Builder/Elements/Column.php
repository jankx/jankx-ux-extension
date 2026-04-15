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
            'span' => '12',
            'class' => '',
        ], $atts);

        $span = intval($options['span']);
        $classes = ['col', 'medium-' . $span];

        if (!empty($options['class'])) {
            $classes[] = esc_attr($options['class']);
        }

        $classString = implode(' ', $classes);

        $html = '<div class="' . esc_attr($classString) . '">';
        $html .= '<div class="col-inner">';
        $html .= do_shortcode($content);
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
