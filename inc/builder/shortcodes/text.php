<?php
/**
 * Text Element for UX Builder
 */

jux_builder_register_element('text', [
    'type' => 'element',
    'name' => 'Text',
    'title' => __('Text Block', 'jankx'),
    'category' => __('Content', 'jankx'),
    'description' => __('Simple text content block.', 'jankx'),
    'thumbnail' => '',
    'wrap' => false,
    'options' => [
        'content' => [
            'type' => 'textarea',
            'heading' => __('Content', 'jankx'),
            'default' => 'Enter your text here...',
        ],
        'text_align' => [
            'type' => 'select',
            'heading' => __('Text Align', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('Left', 'jankx'),
                'center' => __('Center', 'jankx'),
                'right' => __('Right', 'jankx'),
            ],
        ],
        'text_color' => [
            'type' => 'color',
            'heading' => __('Text Color', 'jankx'),
            'default' => '',
        ],
        'font_size' => [
            'type' => 'select',
            'heading' => __('Font Size', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('Default', 'jankx'),
                'small' => __('Small', 'jankx'),
                'medium' => __('Medium', 'jankx'),
                'large' => __('Large', 'jankx'),
            ],
        ],
        'padding' => [
            'type' => 'text',
            'heading' => __('Padding', 'jankx'),
            'default' => '',
        ],
        'class' => [
            'type' => 'text',
            'heading' => __('Custom Class', 'jankx'),
            'default' => '',
        ],
    ],
    'presets' => [
        'default' => [
            'title' => __('Default', 'jankx'),
            'options' => [],
        ],
    ],
    'allow_in' => ['col', 'section', 'ux_block'],
    'template' => function($options, $content) {
        $classes = ['jux-text'];
        $styles = [];
        
        if (!empty($options['text_align'])) {
            $classes[] = 'text-' . esc_attr($options['text_align']);
        }
        
        if (!empty($options['font_size'])) {
            $classes[] = 'is-' . esc_attr($options['font_size']);
        }
        
        if (!empty($options['class'])) {
            $classes[] = esc_attr($options['class']);
        }
        
        if (!empty($options['text_color'])) {
            $styles[] = 'color:' . esc_attr($options['text_color']) . ';';
        }
        
        if (!empty($options['padding'])) {
            $styles[] = 'padding:' . esc_attr($options['padding']) . ';';
        }
        
        $classString = implode(' ', $classes);
        $styleString = implode(' ', $styles);
        
        $textContent = !empty($options['content']) ? $options['content'] : '';
        
        $html = '<div class="' . esc_attr($classString) . '"';
        if ($styleString) {
            $html .= ' style="' . esc_attr($styleString) . '"';
        }
        $html .= '>';
        $html .= wp_kses_post($textContent);
        $html .= '</div>';
        
        return $html;
    },
]);
