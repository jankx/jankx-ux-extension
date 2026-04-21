<?php
/**
 * Global Helper Functions for Jankx UX
 * Compatibility layer with Flatsome global functions
 */

use Jankx\Extensions\JankxUX\Builder\BuilderManager;
use Jankx\Extensions\JankxUX\TemplateManager;

if (!function_exists('add_ux_builder_shortcode')) {
    function add_ux_builder_shortcode($tag, $args) {
        BuilderManager::registerShortcode($tag, $args);
    }
}

if (!function_exists('jux_get_template_part')) {
    function jux_get_template_part($slug, $name = null, $args = []) {
        TemplateManager::getTemplatePart($slug, $name, $args);
    }
}

if (!function_exists('flatsome_ux_builder_template')) {
    function flatsome_ux_builder_template($file) {
        return JUX_PATH . '/template-parts/builder/' . $file;
    }
}

/**
 * Flatsome compatibility: ux_builder_element_style_tag()
 * Generates a <style> block for responsive element CSS.
 *
 * @param string $id    Element HTML id
 * @param array  $args  Map of att_key => ['selector'=>'...','property'=>'...','unit'=>'...']
 * @param array  $atts  The shortcode atts array
 */
if (!function_exists('ux_builder_element_style_tag')) {
    function ux_builder_element_style_tag($id, $args, $atts)
    {
        $css    = '';
        $css_sm = '';
        $css_md = '';

        foreach ($args as $key => $config) {
            $selector = isset($config['selector']) ? $config['selector'] : '';
            $property = isset($config['property']) ? $config['property'] : $key;
            $unit     = isset($config['unit'])     ? $config['unit']     : '';
            $important = !empty($config['important']) ? ' !important' : '';

            // Desktop value
            if (!empty($atts[$key])) {
                $val     = esc_attr($atts[$key]) . $unit;
                $full_sel = '#' . $id . ($selector ? ' ' . $selector : '');
                // Multiple properties (comma-separated)
                foreach (explode(',', $property) as $prop) {
                    $css .= $full_sel . ' { ' . trim($prop) . ':' . $val . $important . '; }';
                }
            }

            // Tablet (__md)
            if (isset($atts[$key . '__md']) && $atts[$key . '__md'] !== null && $atts[$key . '__md'] !== '') {
                $val     = esc_attr($atts[$key . '__md']) . $unit;
                $full_sel = '#' . $id . ($selector ? ' ' . $selector : '');
                foreach (explode(',', $property) as $prop) {
                    $css_md .= $full_sel . ' { ' . trim($prop) . ':' . $val . $important . '; }';
                }
            }

            // Mobile (__sm)
            if (isset($atts[$key . '__sm']) && $atts[$key . '__sm'] !== null && $atts[$key . '__sm'] !== '') {
                $val     = esc_attr($atts[$key . '__sm']) . $unit;
                $full_sel = '#' . $id . ($selector ? ' ' . $selector : '');
                foreach (explode(',', $property) as $prop) {
                    $css_sm .= $full_sel . ' { ' . trim($prop) . ':' . $val . $important . '; }';
                }
            }
        }

        $output = '';
        if ($css)    $output .= '<style>' . $css . '</style>';
        if ($css_md) $output .= '<style>@media (max-width:960px) {' . $css_md . '}</style>';
        if ($css_sm) $output .= '<style>@media (max-width:640px) {' . $css_sm . '}</style>';

        return $output;
    }
}

/**
 * Flatsome compatibility: get_shortcode_inline_css()
 * Returns inline style string from CSS attribute array.
 */
if (!function_exists('get_shortcode_inline_css')) {
    function get_shortcode_inline_css($css_args)
    {
        $styles = [];
        foreach ($css_args as $item) {
            if (is_array($item)) {
                $attr  = isset($item['attribute']) ? $item['attribute'] : '';
                $value = isset($item['value'])     ? $item['value']     : '';
                $unit  = isset($item['unit'])      ? $item['unit']      : '';
                if ($attr && $value !== '') {
                    $styles[] = $attr . ':' . esc_attr($value) . $unit;
                }
            }
        }
        return $styles ? ' style="' . implode(';', $styles) . '"' : '';
    }
}

/**
 * Flatsome compatibility: flatsome_apply_shortcode()
 * Renders a shortcode programmatically from atts array.
 */
if (!function_exists('flatsome_apply_shortcode')) {
    function flatsome_apply_shortcode($tag, $atts = [], $content = '')
    {
        $atts_str = '';
        foreach ($atts as $key => $value) {
            $atts_str .= ' ' . $key . '="' . esc_attr($value) . '"';
        }
        $shortcode = '[' . $tag . $atts_str . ']' . ($content ? $content . '[/' . $tag . ']' : '');
        return do_shortcode($shortcode);
    }
}

/**
 * Flatsome compatibility: flatsome_position_classes()
 * Generates position CSS classes (x50, y50 etc.)
 */
if (!function_exists('flatsome_position_classes')) {
    function flatsome_position_classes($axis, $val, $val_sm = '', $val_md = '')
    {
        $classes = [];
        if ($val !== '')    $classes[] = $axis . intval($val);
        if ($val_md !== '') $classes[] = 'md-' . $axis . intval($val_md);
        if ($val_sm !== '') $classes[] = 'lg-' . $axis . intval($val);
        return implode(' ', $classes);
    }
}
