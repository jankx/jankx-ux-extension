<?php
/**
 * Column Element for UX Builder
 * Mimics Flatsome's [col] shortcode
 */

jux_builder_register_element('col', [
    'type' => 'container',
    'name' => 'Column',
    'title' => __('Column', 'jankx'),
    'category' => __('Layout', 'jankx'),
    'description' => __('Create a column inside a row.', 'jankx'),
    'thumbnail' => JANKX_UX_URL . 'assets/img/icons/col.png',
    'wrap' => true,
    'options' => [
        'span' => [
            'type' => 'select',
            'heading' => __('Column Width', 'jankx'),
            'default' => '12',
            'options' => [
                '1' => '1/12 (8%)',
                '2' => '2/12 (17%)',
                '3' => '3/12 (25%)',
                '4' => '4/12 (33%)',
                '5' => '5/12 (42%)',
                '6' => '6/12 (50%)',
                '7' => '7/12 (58%)',
                '8' => '8/12 (67%)',
                '9' => '9/12 (75%)',
                '10' => '10/12 (83%)',
                '11' => '11/12 (92%)',
                '12' => '12/12 (100%)',
            ],
        ],
        'span__md' => [
            'type' => 'select',
            'heading' => __('Column Width (Tablet)', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('Inherit', 'jankx'),
                '1' => '1/12',
                '2' => '2/12',
                '3' => '3/12',
                '4' => '4/12',
                '5' => '5/12',
                '6' => '6/12',
                '7' => '7/12',
                '8' => '8/12',
                '9' => '9/12',
                '10' => '10/12',
                '11' => '11/12',
                '12' => '12/12',
            ],
        ],
        'span__sm' => [
            'type' => 'select',
            'heading' => __('Column Width (Mobile)', 'jankx'),
            'default' => '12',
            'options' => [
                '' => __('Inherit', 'jankx'),
                '1' => '1/12',
                '2' => '2/12',
                '3' => '3/12',
                '4' => '4/12',
                '5' => '5/12',
                '6' => '6/12',
                '7' => '7/12',
                '8' => '8/12',
                '9' => '9/12',
                '10' => '10/12',
                '11' => '11/12',
                '12' => '12/12',
            ],
        ],
        'padding' => [
            'type' => 'text',
            'heading' => __('Padding', 'jankx'),
            'default' => '',
            'description' => __('e.g. 30px, 5%', 'jankx'),
        ],
        'margin' => [
            'type' => 'text',
            'heading' => __('Margin', 'jankx'),
            'default' => '',
        ],
        'bg_color' => [
            'type' => 'color',
            'heading' => __('Background Color', 'jankx'),
            'default' => '',
        ],
        'bg_radius' => [
            'type' => 'text',
            'heading' => __('Background Radius', 'jankx'),
            'default' => '',
            'description' => __('e.g. 5px, 10%', 'jankx'),
        ],
        'depth' => [
            'type' => 'slider',
            'heading' => __('Depth', 'jankx'),
            'default' => '',
            'min' => 0,
            'max' => 5,
            'step' => 1,
        ],
        'animate' => [
            'type' => 'select',
            'heading' => __('Animate', 'jankx'),
            'default' => '',
            'options' => [
                '' => __('None', 'jankx'),
                'fadeIn' => __('Fade In', 'jankx'),
                'fadeInUp' => __('Fade In Up', 'jankx'),
                'fadeInDown' => __('Fade In Down', 'jankx'),
                'fadeInLeft' => __('Fade In Left', 'jankx'),
                'fadeInRight' => __('Fade In Right', 'jankx'),
                'bounceIn' => __('Bounce In', 'jankx'),
                'bounceInUp' => __('Bounce In Up', 'jankx'),
                'slideInUp' => __('Slide In Up', 'jankx'),
                'slideInDown' => __('Slide In Down', 'jankx'),
            ],
        ],
        'parallax' => [
            'type' => 'slider',
            'heading' => __('Parallax', 'jankx'),
            'default' => '',
            'min' => -10,
            'max' => 10,
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
        'half' => [
            'title' => __('1/2 Column', 'jankx'),
            'options' => [
                'span' => '6',
            ],
        ],
        'third' => [
            'title' => __('1/3 Column', 'jankx'),
            'options' => [
                'span' => '4',
            ],
        ],
        'quarter' => [
            'title' => __('1/4 Column', 'jankx'),
            'options' => [
                'span' => '3',
            ],
        ],
    ],
    'allow_in' => ['row'],
    'template' => function($options, $content) {
        $span = isset($options['span']) ? intval($options['span']) : 12;
        $classes = ['col', 'medium-' . $span];
        $styles = [];
        
        // Responsive classes
        if (!empty($options['span__md'])) {
            $classes[] = 'small-' . intval($options['span__md']);
        }
        
        if (!empty($options['span__sm'])) {
            $classes[] = 'small-' . intval($options['span__sm']);
        }
        
        // Custom class
        if (!empty($options['class'])) {
            $classes[] = esc_attr($options['class']);
        }
        
        // Depth
        if (!empty($options['depth'])) {
            $classes[] = 'box-shadow-' . intval($options['depth']);
        }
        
        // Visibility
        if (!empty($options['visibility'])) {
            $classes[] = esc_attr($options['visibility']) . '-for-small';
        }
        
        // Animation
        if (!empty($options['animate'])) {
            $classes[] = 'animated';
            $classes[] = 'fadeIn'; // Base animation class
            $classes[] = esc_attr($options['animate']);
        }
        
        // Parallax
        if (!empty($options['parallax'])) {
            $classes[] = 'parallax';
            $styles[] = '--parallax:' . floatval($options['parallax']) . ';';
        }
        
        // Background color
        if (!empty($options['bg_color'])) {
            $styles[] = 'background-color:' . esc_attr($options['bg_color']) . ';';
        }
        
        // Background radius
        if (!empty($options['bg_radius'])) {
            $styles[] = 'border-radius:' . esc_attr($options['bg_radius']) . ';';
        }
        
        // Padding
        if (!empty($options['padding'])) {
            $styles[] = 'padding:' . esc_attr($options['padding']) . ';';
        }
        
        // Margin
        if (!empty($options['margin'])) {
            $styles[] = 'margin:' . esc_attr($options['margin']) . ';';
        }
        
        $classString = implode(' ', array_unique($classes));
        $styleString = implode(' ', $styles);
        
        $html = '<div class="' . esc_attr($classString) . '"';
        if ($styleString) {
            $html .= ' style="' . esc_attr($styleString) . '"';
        }
        $html .= '>';
        $html .= '<div class="col-inner">';
        $html .= $content;
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    },
]);
