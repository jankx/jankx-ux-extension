<?php
/**
 * Builder Element: UX Slider
 * Exact parity with Flatsome's builder registration.
 */

add_ux_builder_shortcode( 'ux_slider', array(
    'type' => 'container',
    'name' => __( 'Slider', 'jankx' ),
    'category' => __( 'Layout', 'jankx' ),
    'options' => array(
        'type' => array(
          'type' => 'select',
          'heading' => 'Type',
          'default' => 'slide',
          'options' => array(
            'slide' => 'Slide',
            'fade' => 'Fade',
          ),
        ),
        'auto_slide' => array(
            'type' => 'radio-buttons',
            'heading' => __('Auto slide', 'jankx'),
            'default' => 'true',
            'options' => array(
                'false'  => array( 'title' => 'Off'),
                'true'  => array( 'title' => 'On'),
            ),
        ),
        'timer' => array(
            'type' => 'textfield',
            'heading' => 'Timer (ms)',
            'default' => 6000,
        ),
    ),
) );
