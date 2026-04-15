<?php
/**
 * JUX Builder Helper Functions
 * Mimics Flatsome's helper functions
 */

use Jankx\Extensions\JankxUX\Builder\Core\Application;

/**
 * Get builder application instance
 */
if (!function_exists('jux_builder')) {
    function jux_builder($name = null) {
        return Application::getInstance()->resolve($name);
    }
}

/**
 * Check if builder is active
 */
if (!function_exists('jux_builder_is_active')) {
    function jux_builder_is_active() {
        return isset($_GET['app']) && $_GET['app'] === 'jux';
    }
}

/**
 * Check if in editor mode
 */
if (!function_exists('jux_builder_is_editor')) {
    function jux_builder_is_editor() {
        return jux_builder_is_active() && isset($_GET['type']) && $_GET['type'] === 'editor';
    }
}

/**
 * Check if in iframe preview mode
 */
if (!function_exists('jux_builder_is_iframe')) {
    function jux_builder_is_iframe() {
        return isset($_GET['jux-preview']) && $_GET['jux-preview'] === 'true';
    }
}

/**
 * Get builder version
 */
if (!function_exists('jux_builder_version')) {
    function jux_builder_version() {
        return defined('JANKX_UX_VERSION') ? JANKX_UX_VERSION : '1.0.0';
    }
}

/**
 * Get builder path
 */
if (!function_exists('jux_builder_path')) {
    function jux_builder_path($path = '') {
        $base = JANKX_UX_PATH . 'src/Builder/';
        return $base . ltrim($path, '/');
    }
}

/**
 * Get builder URL
 */
if (!function_exists('jux_builder_url')) {
    function jux_builder_url($path = '') {
        $base = JANKX_UX_URL . 'src/Builder/';
        return $base . ltrim($path, '/');
    }
}

/**
 * Get asset URL
 */
if (!function_exists('jux_builder_asset')) {
    function jux_builder_asset($path = '') {
        return JANKX_UX_URL . 'assets/' . ltrim($path, '/');
    }
}

/**
 * Get supported post types
 */
if (!function_exists('jux_builder_post_types')) {
    function jux_builder_post_types() {
        return apply_filters('jux_builder_post_types', [
            'page' => __('Pages', 'jankx'),
            'post' => __('Posts', 'jankx'),
            'ux_block' => __('UX Blocks', 'jankx'),
        ]);
    }
}

/**
 * Get editor link for a post
 */
if (!function_exists('jux_builder_edit_link')) {
    function jux_builder_edit_link($post_id) {
        return admin_url('post.php?post=' . $post_id . '&action=edit&app=jux&type=editor');
    }
}

/**
 * Get preview link for a post
 */
if (!function_exists('jux_builder_preview_link')) {
    function jux_builder_preview_link($post_id) {
        $url = get_permalink($post_id);
        return add_query_arg('jux-preview', 'true', $url);
    }
}

/**
 * Register an element
 */
if (!function_exists('jux_builder_register_element')) {
    function jux_builder_register_element($tag, $config = []) {
        add_action('jux_builder_setup', function() use ($tag, $config) {
            jux_builder('elements')->register($tag, $config);
        });
    }
}

/**
 * Register a template
 */
if (!function_exists('jux_builder_register_template')) {
    function jux_builder_register_template($id, $config = []) {
        add_action('jux_builder_setup', function() use ($id, $config) {
            jux_builder('templates')->register($id, $config);
        });
    }
}

/**
 * Register a component
 */
if (!function_exists('jux_builder_register_component')) {
    function jux_builder_register_component($name, $config = []) {
        add_action('jux_builder_setup', function() use ($name, $config) {
            jux_builder('components')->register($name, $config);
        });
    }
}

/**
 * Render a shortcode preview in builder
 */
if (!function_exists('jux_builder_render_shortcode')) {
    function jux_builder_render_shortcode($shortcode, $content = '') {
        if (!empty($content)) {
            $shortcode = str_replace(']', ']' . $content . '[/' . strtok($shortcode, ' ') . ']', $shortcode);
        }
        return do_shortcode($shortcode);
    }
}

/**
 * Parse shortcode attributes to options
 */
if (!function_exists('jux_builder_parse_atts')) {
    function jux_builder_parse_atts($atts, $defaults = []) {
        $parsed = shortcode_atts($defaults, $atts);
        
        // Handle responsive suffixes like __md, __sm
        $responsive = [];
        foreach ($parsed as $key => $value) {
            if (preg_match('/^(.+)__(md|sm|lg)$/', $key, $matches)) {
                $baseKey = $matches[1];
                $breakpoint = $matches[2];
                if (!isset($responsive[$baseKey])) {
                    $responsive[$baseKey] = [];
                }
                $responsive[$baseKey][$breakpoint] = $value;
                unset($parsed[$key]);
            }
        }
        
        // Merge responsive back
        foreach ($responsive as $key => $breakpoints) {
            $parsed[$key . '_responsive'] = $breakpoints;
        }
        
        return $parsed;
    }
}

/**
 * Build shortcode from options
 */
if (!function_exists('jux_builder_build_shortcode')) {
    function jux_builder_build_shortcode($tag, $options = [], $content = '') {
        $atts = [];
        
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                // Handle responsive values
                foreach ($value as $bp => $bpValue) {
                    if ($bpValue !== '' && $bpValue !== null) {
                        $atts[$key . '__' . $bp] = $bpValue;
                    }
                }
            } elseif ($value !== '' && $value !== null && $value !== false) {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                $atts[$key] = $value;
            }
        }
        
        $attString = '';
        foreach ($atts as $key => $value) {
            $attString .= ' ' . $key . '="' . esc_attr($value) . '"';
        }
        
        if (!empty($content)) {
            return '[' . $tag . $attString . ']' . $content . '[/' . $tag . ']';
        }
        
        return '[' . $tag . $attString . ']';
    }
}

/**
 * Get dependency handles
 */
if (!function_exists('jux_builder_deps')) {
    function jux_builder_deps($wp_scripts_or_styles, $handle) {
        $deps = [];
        $item = $wp_scripts_or_styles->registered[$handle] ?? null;
        
        if ($item) {
            foreach ($item->deps as $dep) {
                $deps[] = $dep;
                $deps = array_merge($deps, jux_builder_deps($wp_scripts_or_styles, $dep));
            }
        }
        
        return array_unique($deps);
    }
}

/**
 * Check if shortcode is nested
 */
if (!function_exists('jux_builder_is_nested')) {
    function jux_builder_is_nested($tag) {
        $element = jux_builder('elements')->get($tag);
        return $element && !empty($element['wrap']);
    }
}

/**
 * Get element presets
 */
if (!function_exists('jux_builder_get_presets')) {
    function jux_builder_get_presets($tag) {
        $element = jux_builder('elements')->get($tag);
        return $element ? ($element['presets'] ?? []) : [];
    }
}
