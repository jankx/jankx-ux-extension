<?php
/**
 * Global Helper Functions for Jankx UX
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
