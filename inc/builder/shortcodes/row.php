<?php
/**
 * Row Element for UX Builder
 * Mimics Flatsome's [row] shortcode
 */

jux_builder_register_element('row', [
    'type' => 'container',
    'name' => 'Row',
    'title' => __('Row', 'jankx'),
    'category' => __('Layout', 'jankx'),
    'description' => __('Create a row container for columns.', 'jankx'),
    'thumbnail' => JANKX_UX_URL . 'assets/img/icons/row.png',
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
                'box' => __('Box', 'jankx'),
            ],
        ],
        'col_style' => [
            'type' => 'select',
            'heading' => __('Column Style', 'jankx'),
            'default' => 'default',
            'options' => [
                'default' => __('Default', 'jankx'),
                'divider' => __('Divider', 'jankx'),
                'dashed' => __('Dashed', 'jankx'),
                'solid' => __('Solid', 'jankx'),
            ],
        ],
        'width' => [
            'type' => 'select',
            'heading' => __('Row Width', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('Container', 'jankx'),
                'full-width' => __('Full Width', 'jankx'),
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
        'h_align' => [
            'type' => 'select',
            'heading' => __('Horizontal Align', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('Left', 'jankx'),
                'center' => __('Center', 'jankx'),
                'right' => __('Right', 'jankx'),
            ],
        ],
        'padding' => [
            'type' => 'text',
            'heading' => __('Padding', 'jankx'),
            'default' => '',
            'description' => __('e.g. 30px, 5%, 2em', 'jankx'),
        ],
        'margin' => [
            'type' => 'text',
            'heading' => __('Margin', 'jankx'),
            'default' => '',
            'description' => __('e.g. 30px, 5%, 2em', 'jankx'),
        ],
        'depth' => [
            'type' => 'slider',
            'heading' => __('Depth', 'jankx'),
            'default' => '',
            'min' => 0,
            'max' => 5,
            'step' => 1,
        ],
        'depth_hover' => [
            'type' => 'slider',
            'heading' => __('Depth on Hover', 'jankx'),
            'default' => '',
            'min' => 0,
            'max' => 5,
            'step' => 1,
        ],
        'class' => [
            'type' => 'text',
            'heading' => __('Custom Class', 'jankx'),
            'default' => '',
        ],
        'visibility' => [
            'type' => 'select',
            'heading' => __('Visibility', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('All', 'jankx'),
                'visible' => __('Visible', 'jankx'),
                'hidden' => __('Hidden', 'jankx'),
            ],
        ],
    ],
    'presets' => [
        'default' => [
            'title' => __('Default Row', 'jankx'),
            'options' => [],
        ],
        'full-width' => [
            'title' => __('Full Width', 'jankx'),
            'options' => [
                'width' => 'full-width',
            ],
        ],
        'collapse' => [
            'title' => __('Collapsed', 'jankx'),
            'options' => [
                'style' => 'collapse',
            ],
        ],
    ],
    'allow_in' => ['section', 'ux_block'],
    'template' => function($options, $content) {
        $classes = ['row'];
        $styles = [];
        
        if (!empty($options['style']) && $options['style'] !== 'default') {
            $classes[] = 'row-' . esc_attr($options['style']);
        }
        
        if (!empty($options['col_style']) && $options['col_style'] !== 'default') {
            $classes[] = 'row-col-' . esc_attr($options['col_style']);
        }
        
        if (!empty($options['v_align'])) {
            $classes[] = 'row-valign-' . esc_attr($options['v_align']);
        }
        
        if (!empty($options['h_align'])) {
            $classes[] = 'row-align-' . esc_attr($options['h_align']);
        }
        
        if (!empty($options['class'])) {
            $classes[] = esc_attr($options['class']);
        }
        
        if (!empty($options['depth'])) {
            $classes[] = 'box-shadow-' . intval($options['depth']);
        }
        
        if (!empty($options['depth_hover'])) {
            $classes[] = 'box-shadow-' . intval($options['depth_hover']) . '-hover';
        }
        
        if (!empty($options['visibility'])) {
            $classes[] = esc_attr($options['visibility']) . '-for-small';
        }
        
        if (!empty($options['padding'])) {
            $styles[] = 'padding:' . esc_attr($options['padding']) . ';';
        }
        
        if (!empty($options['margin'])) {
            $styles[] = 'margin:' . esc_attr($options['margin']) . ';';
        }
        
        $classString = implode(' ', $classes);
        $styleString = implode(' ', $styles);
        
        $html = '<div class="' . esc_attr($classString) . '"';
        if ($styleString) {
            $html .= ' style="' . esc_attr($styleString) . '"';
        }
        $html .= '>';
        $html .= $content;
        $html .= '</div>';
        
        return $html;
    },
]);
