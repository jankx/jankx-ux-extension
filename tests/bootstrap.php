<?php
/**
 * PHPUnit Bootstrap for Jankx UX Extension Tests
 *
 * Initializes Brain Monkey for mocking WordPress functions
 */

// Autoload Composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize Brain Monkey
\Brain\Monkey\setUp();

// Define WordPress constants if not defined
define('JANKX_UX_DIR', dirname(__DIR__));
define('JANKX_UX_URL', 'http://example.com/wp-content/themes/jankx/extensions/jankx-ux/');
define('JANKX_UX_VERSION', '1.0.0');

// Load the plugin files
require_once JANKX_UX_DIR . '/src/Builder/ElementRegistry.php';
require_once JANKX_UX_DIR . '/src/Builder/Elements/AbstractElement.php';

// Mock common WordPress functions
if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

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

// Mock WordPress functions that are widely used
if (!function_exists('add_action')) {
    function add_action() {}
}
if (!function_exists('add_filter')) {
    function add_filter() {}
}
