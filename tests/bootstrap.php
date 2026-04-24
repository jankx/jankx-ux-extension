<?php
/**
 * PHPUnit Bootstrap for Jankx UX Extension Tests
 */

// 1. Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Initialize Brain Monkey (Patchwork is started here)
\Brain\Monkey\setUp();

// 3. Define WordPress constants
define('JANKX_UX_DIR', dirname(__DIR__));
define('JANKX_UX_URL', 'http://example.com/wp-content/themes/jankx/extensions/jankx-ux/');
define('JANKX_UX_VERSION', '1.0.0');

/**
 * Define WordPress mocks that do NOT conflict with Brain Monkey's redefinition
 * Use Brain\Monkey\Functions\when() for global defaults that should persist
 */

\Brain\Monkey\Functions\stubs([
    '__', 'esc_html__', 'esc_attr__', '_x', '_n', 'esc_html_x', 'esc_attr_x',
    'wp_kses_post', 'wp_unslash', 'esc_attr',
    'sanitize_text_field', 'sanitize_key', 'sanitize_title', 'absint',
    'do_shortcode', 'esc_js', 'esc_url', 'esc_textarea'
]);

// Handle functions that might be called before/during Brain\Monkey initialization
if (!function_exists('shortcode_atts')) {
    function shortcode_atts($pairs, $atts) {
        $atts = (array) $atts;
        $out = [];
        foreach ($pairs as $name => $default) {
            $out[$name] = isset($atts[$name]) ? $atts[$name] : $default;
        }
        return $out;
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($a) { return $a instanceof WP_Error; }
}

if (!class_exists('WP_Error')) {
    class WP_Error {
        public function get_error_message() { return 'Error message'; }
    }
}

// 4. Load the plugin files
require_once JANKX_UX_DIR . '/src/Builder/ElementRegistry.php';
require_once JANKX_UX_DIR . '/src/Builder/Elements/AbstractElement.php';
require_once JANKX_UX_DIR . '/src/Builder/Elements/Text.php';
require_once JANKX_UX_DIR . '/src/Builder/Core/Ajax/AjaxManager.php';
require_once JANKX_UX_DIR . '/src/Builder/Core/ShortcodeParser.php';
require_once JANKX_UX_DIR . '/src/Shortcodes/ShortcodeManager.php';
