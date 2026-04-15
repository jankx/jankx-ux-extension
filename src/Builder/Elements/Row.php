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
                '_label' => [
                    'type' => 'text',
                    'heading' => __('Element Name', 'jankx'),
                    'default' => '',
                    'placeholder' => __('Custom name for this element', 'jankx'),
                ],
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
        // Parse atts - match Flatsome exactly
        $options = shortcode_atts([
            '_id' => 'row-' . rand(),
            '_jux_id' => '',
            'style' => '',
            'col_style' => '',
            'label' => '',
            'border_color' => '',
            'width' => '',
            'custom_width' => '',
            'class' => '',
            'visibility' => '',
            'v_align' => '',
            'h_align' => '',
            'depth' => '',
            'depth_hover' => '',
            'padding' => '',
            'col_bg' => '',
            'col_bg_radius' => '',
        ], $atts);

        // Hide if visibility is hidden
        if ($options['visibility'] === 'hidden') return '';

        $classes = ['row'];

        // Add Row style
        if (!empty($options['style'])) $classes[] = 'row-' . esc_attr($options['style']);

        // Add Row Width
        if ($options['width'] === 'full-width') $classes[] = 'row-full-width';

        // Column Vertical Align - Flatsome uses 'align-{val}'
        if (!empty($options['v_align'])) $classes[] = 'align-' . esc_attr($options['v_align']);

        // Column Horizontal Align
        if (!empty($options['h_align'])) $classes[] = 'align-' . esc_attr($options['h_align']);

        // Column style
        if (!empty($options['col_style'])) $classes[] = 'row-' . esc_attr($options['col_style']);

        // Custom Class & Visibility
        if (!empty($options['class'])) $classes[] = esc_attr($options['class']);
        if (!empty($options['visibility'])) $classes[] = esc_attr($options['visibility']);

        // Depth - Flatsome uses 'row-box-shadow-{n}'
        if (!empty($options['depth'])) $classes[] = 'row-box-shadow-' . intval($options['depth']);
        if (!empty($options['depth_hover'])) $classes[] = 'row-box-shadow-' . intval($options['depth_hover']) . '-hover';

        // Custom Width style
        $custom_width_attr = '';
        if ($options['width'] === 'custom' && !empty($options['custom_width'])) {
            $custom_width_attr = 'style="max-width:' . esc_attr($options['custom_width']) . '"';
        }

        $classString = implode(' ', $classes);
        $id = esc_attr($options['_id']);

        return '<div class="' . esc_attr($classString) . '" ' . $custom_width_attr . ' id="' . $id . '">'
            . do_shortcode($content)
            . '</div>';
    }
}
