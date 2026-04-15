<?php
/**
 * Button Element for UX Builder
 */

jux_builder_register_element('button', [
    'type' => 'element',
    'name' => 'Button',
    'title' => __('Button', 'jankx'),
    'category' => __('Content', 'jankx'),
    'description' => __('Call to action button.', 'jankx'),
    'thumbnail' => '',
    'wrap' => false,
    'options' => [
        'text' => [
            'type' => 'text',
            'heading' => __('Button Text', 'jankx'),
            'default' => 'Click Me',
        ],
        'link' => [
            'type' => 'text',
            'heading' => __('Link', 'jankx'),
            'default' => '',
        ],
        'target' => [
            'type' => 'select',
            'heading' => __('Target', 'jankx'),
            'default' => '_self',
            'options' => [
                '_self' => __('Same Window', 'jankx'),
                '_blank' => __('New Window', 'jankx'),
            ],
        ],
        'style' => [
            'type' => 'select',
            'heading' => __('Button Style', 'jankx'),
            'default' => 'primary',
            'options' => [
                'primary' => __('Primary', 'jankx'),
                'secondary' => __('Secondary', 'jankx'),
                'success' => __('Success', 'jankx'),
                'alert' => __('Alert', 'jankx'),
                'outline' => __('Outline', 'jankx'),
            ],
        ],
        'size' => [
            'type' => 'select',
            'heading' => __('Size', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('Default', 'jankx'),
                'small' => __('Small', 'jankx'),
                'large' => __('Large', 'jankx'),
                'expand' => __('Expand', 'jankx'),
            ],
        ],
        'radius' => [
            'type' => 'select',
            'heading' => __('Border Radius', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('Default', 'jankx'),
                'round' => __('Round', 'jankx'),
                'roundest' => __('Roundest', 'jankx'),
            ],
        ],
        'icon' => [
            'type' => 'text',
            'heading' => __('Icon (dashicons)', 'jankx'),
            'default' => '',
            'description' => __('e.g. dashicons-arrow-right-alt', 'jankx'),
        ],
        'depth' => [
            'type' => 'slider',
            'heading' => __('Depth', 'jankx'),
            'default' => '',
            'min' => 0,
            'max' => 5,
        ],
        'class' => [
            'type' => 'text',
            'heading' => __('Custom Class', 'jankx'),
            'default' => '',
        ],
    ],
    'presets' => [
        'default' => [
            'title' => __('Default Button', 'jankx'),
            'options' => [],
        ],
        'outline' => [
            'title' => __('Outline Button', 'jankx'),
            'options' => [
                'style' => 'outline',
            ],
        ],
    ],
    'allow_in' => ['col', 'section', 'ux_block'],
    'template' => function($options, $content) {
        $classes = ['button'];
        $styles = [];
        
        if (!empty($options['style'])) {
            $classes[] = esc_attr($options['style']);
        }
        
        if (!empty($options['size'])) {
            $classes[] = 'is-' . esc_attr($options['size']);
        }
        
        if (!empty($options['radius'])) {
            $classes[] = 'is-' . esc_attr($options['radius']);
        }
        
        if (!empty($options['depth'])) {
            $classes[] = 'box-shadow-' . intval($options['depth']);
        }
        
        if (!empty($options['class'])) {
            $classes[] = esc_attr($options['class']);
        }
        
        $text = !empty($options['text']) ? esc_html($options['text']) : 'Button';
        $link = !empty($options['link']) ? esc_url($options['link']) : '#';
        $target = !empty($options['target']) ? esc_attr($options['target']) : '_self';
        $icon = !empty($options['icon']) ? '<i class="dashicons ' . esc_attr($options['icon']) . '"></i>' : '';
        
        $classString = implode(' ', $classes);
        
        $html = '<a href="' . $link . '" target="' . $target . '" class="' . esc_attr($classString) . '">';
        $html .= $icon . ' ' . $text;
        $html .= '</a>';
        
        return $html;
    },
]);
