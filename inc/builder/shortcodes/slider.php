<?php
/**
 * Slider Element for UX Builder
 */

jux_builder_register_element('slider', [
    'type' => 'container',
    'name' => 'Slider',
    'title' => __('Slider', 'jankx'),
    'category' => __('Content', 'jankx'),
    'description' => __('Image or content slider.', 'jankx'),
    'thumbnail' => '',
    'wrap' => true,
    'options' => [
        'timer' => [
            'type' => 'text',
            'heading' => __('Autoplay Timer', 'jankx'),
            'default' => '5000',
            'description' => __('Milliseconds, 0 to disable', 'jankx'),
        ],
        'bullets' => [
            'type' => 'checkbox',
            'heading' => __('Show Bullets', 'jankx'),
            'default' => 'true',
        ],
        'arrows' => [
            'type' => 'checkbox',
            'heading' => __('Show Arrows', 'jankx'),
            'default' => 'true',
        ],
        'auto_slide' => [
            'type' => 'checkbox',
            'heading' => __('Auto Slide', 'jankx'),
            'default' => 'true',
        ],
        'pause_hover' => [
            'type' => 'checkbox',
            'heading' => __('Pause on Hover', 'jankx'),
            'default' => 'true',
        ],
        'slide_width' => [
            'type' => 'text',
            'heading' => __('Slide Width', 'jankx'),
            'default' => '',
            'description' => __('e.g. 500px, 50%', 'jankx'),
        ],
        'infinitive' => [
            'type' => 'checkbox',
            'heading' => __('Infinite Loop', 'jankx'),
            'default' => 'true',
        ],
        'draggable' => [
            'type' => 'checkbox',
            'heading' => __('Draggable', 'jankx'),
            'default' => 'true',
        ],
        'class' => [
            'type' => 'text',
            'heading' => __('Custom Class', 'jankx'),
            'default' => '',
        ],
    ],
    'presets' => [
        'default' => [
            'title' => __('Default Slider', 'jankx'),
            'options' => [],
        ],
    ],
    'allow_in' => ['col', 'section', 'ux_block'],
    'template' => function($options, $content) {
        $classes = ['jux-slider', 'slider'];
        $dataAttrs = [];
        
        if (!empty($options['class'])) {
            $classes[] = esc_attr($options['class']);
        }
        
        // Data attributes for JS
        if (!empty($options['timer'])) {
            $dataAttrs[] = 'data-timer="' . intval($options['timer']) . '"';
        }
        
        if (isset($options['auto_slide']) && $options['auto_slide'] === 'true') {
            $dataAttrs[] = 'data-auto="true"';
        }
        
        if (isset($options['bullets']) && $options['bullets'] === 'true') {
            $dataAttrs[] = 'data-bullets="true"';
        }
        
        if (isset($options['arrows']) && $options['arrows'] === 'true') {
            $dataAttrs[] = 'data-arrows="true"';
        }
        
        if (isset($options['pause_hover']) && $options['pause_hover'] === 'true') {
            $dataAttrs[] = 'data-pause-hover="true"';
        }
        
        if (!empty($options['slide_width'])) {
            $dataAttrs[] = 'data-slide-width="' . esc_attr($options['slide_width']) . '"';
        }
        
        if (isset($options['infinitive']) && $options['infinitive'] === 'true') {
            $dataAttrs[] = 'data-infinite="true"';
        }
        
        if (isset($options['draggable']) && $options['draggable'] === 'true') {
            $dataAttrs[] = 'data-draggable="true"';
        }
        
        $classString = implode(' ', $classes);
        $dataString = implode(' ', $dataAttrs);
        
        $html = '<div class="' . esc_attr($classString) . '" ' . $dataString . '>';
        $html .= '<div class="slider-wrapper">';
        $html .= $content;
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    },
]);
