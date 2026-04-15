<?php
namespace Jankx\Extensions\JankxUX\Builder\Elements;

/**
 * Row Element - PSR-4 Static Class
 */
class Row extends AbstractElement
{
    protected static $tag = 'row';

    protected static function getConfig()
    {
        return [
            'type' => 'container',
            'name' => 'Row',
            'title' => __('Row', 'jankx'),
            'category' => __('Layout', 'jankx'),
            'description' => __('Create a row container for columns.', 'jankx'),
            'thumbnail' => '',
            'wrap' => true,
            'options' => [
                'style' => [
                    'type' => 'select',
                    'heading' => __('Row Style', 'jankx'),
                    'default' => 'default',
                    'options' => [
                        'default' => __('Default', 'jankx'),
                        'full' => __('Full Width', 'jankx'),
                        'collapse' => __('Collapse', 'jankx'),
                    ],
                ],
                'v_align' => [
                    'type' => 'select',
                    'heading' => __('Vertical Align', 'jankx'),
                    'default' => '',
                    'options' => [
                        '' => __('Top', 'jankx'),
                        'middle' => __('Middle', 'jankx'),
                        'bottom' => __('Bottom', 'jankx'),
                    ],
                ],
                'class' => [
                    'type' => 'text',
                    'heading' => __('Custom Class', 'jankx'),
                    'default' => '',
                ],
            ],
            'presets' => [
                'default' => [
                    'title' => __('Default Row', 'jankx'),
                    'options' => [],
                ],
            ],
            'allow_in' => ['section', 'ux_block'],
        ];
    }

    public static function render($atts = [], $content = '')
    {
        $options = shortcode_atts([
            'style' => 'default',
            'v_align' => '',
            'class' => '',
        ], $atts);

        $classes = ['row'];

        if (!empty($options['style']) && $options['style'] !== 'default') {
            $classes[] = 'row-' . esc_attr($options['style']);
        }

        if (!empty($options['v_align'])) {
            $classes[] = 'row-valign-' . esc_attr($options['v_align']);
        }

        if (!empty($options['class'])) {
            $classes[] = esc_attr($options['class']);
        }

        $classString = implode(' ', $classes);

        $html = '<div class="' . esc_attr($classString) . '">';
        $html .= do_shortcode($content);
        $html .= '</div>';

        return $html;
    }
}
