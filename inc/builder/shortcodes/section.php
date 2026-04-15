<?php
/**
 * Section Element for UX Builder
 */

jux_builder_register_element('section', [
    'type' => 'container',
    'name' => 'Section',
    'title' => __('Section', 'jankx'),
    'category' => __('Layout', 'jankx'),
    'description' => __('Full-width content section.', 'jankx'),
    'thumbnail' => '',
    'wrap' => true,
    'options' => [
        'label' => [
            'type' => 'text',
            'heading' => __('Label', 'jankx'),
            'default' => '',
            'description' => __('Internal label for organization', 'jankx'),
        ],
        'bg_color' => [
            'type' => 'color',
            'heading' => __('Background Color', 'jankx'),
            'default' => '',
        ],
        'padding' => [
            'type' => 'select',
            'heading' => __('Padding', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('Default', 'jankx'),
                'small' => __('Small', 'jankx'),
                'medium' => __('Medium', 'jankx'),
                'large' => __('Large', 'jankx'),
            ],
        ],
        'class' => [
            'type' => 'text',
            'heading' => __('Custom Class', 'jankx'),
            'default' => '',
        ],
        'id' => [
            'type' => 'text',
            'heading' => __('Section ID', 'jankx'),
            'default' => '',
            'description' => __('For anchor links', 'jankx'),
        ],
    ],
    'presets' => [
        'default' => [
            'title' => __('Default Section', 'jankx'),
            'options' => [],
        ],
    ],
    'allow_in' => [], // Top level
    'template' => function($options, $content) {
        $classes = ['section'];
        $styles = [];
        $id = '';
        
        if (!empty($options['padding'])) {
            $classes[] = 'section-' . esc_attr($options['padding']);
        }
        
        if (!empty($options['class'])) {
            $classes[] = esc_attr($options['class']);
        }
        
        if (!empty($options['bg_color'])) {
            $styles[] = 'background-color:' . esc_attr($options['bg_color']) . ';';
        }
        
        if (!empty($options['id'])) {
            $id = ' id="' . esc_attr($options['id']) . '"';
        }
        
        $classString = implode(' ', $classes);
        $styleString = implode(' ', $styles);
        
        $html = '<section class="' . esc_attr($classString) . '"' . $id;
        if ($styleString) {
            $html .= ' style="' . esc_attr($styleString) . '"';
        }
        $html .= '>';
        $html .= '<div class="section-content">';
        $html .= $content;
        $html .= '</div>';
        $html .= '</section>';
        
        return $html;
    },
]);
