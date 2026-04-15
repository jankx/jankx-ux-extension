<?php
/**
 * PHPUnit Bootstrap for Jankx UX Tests
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Mock WordPress functions that are widely used
if (!function_exists('add_action')) {
    function add_action() {}
}
if (!function_exists('add_filter')) {
    function add_filter() {}
}
if (!function_exists('__')) {
    function __($text, $domain) { return $text; }
}

// Initialize Brain\Monkey
\Brain\Monkey\setUp();
