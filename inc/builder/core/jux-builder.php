<?php
/**
 * JUX Builder Core Loader
 * Mimics Flatsome's ux-builder.php
 */

// If this file is called directly, abort.
if (!defined('WPINC')) die;

// Get Jankx UX version
$version = defined('JANKX_UX_VERSION') ? JANKX_UX_VERSION : '1.0.0';

// Defines
define('JUX_BUILDER_VERSION', $version);
define('JUX_BUILDER_PATH', JANKX_UX_PATH . 'src/Builder/Core');
define('JUX_BUILDER_URL', JANKX_UX_URL);

// Required files
require_once JUX_BUILDER_PATH . '/helpers.php';

// Always load Application (needed for AJAX)
add_action('init', function() {
    \Jankx\Extensions\JankxUX\Builder\Core\Application::getInstance();
}, 20);

// Stop UI-specific features if builder not active
if (!jux_builder_is_active()) {
    return;
}

// Register the autoloader
spl_autoload_register(function ($class_name) {
    $prefix = 'Jankx\\Extensions\\JankxUX\\Builder\\Core\\';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class_name, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class_name, $len);
    $class_file = JUX_BUILDER_PATH . '/' . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($class_file)) {
        require_once $class_file;
    }
});

// Initialize Application (theme context, not plugin)
add_action('after_setup_theme', function() {
    \Jankx\Extensions\JankxUX\Builder\Core\Application::getInstance();
}, 20);

// Also load in admin/AJAX context
add_action('admin_init', function() {
    \Jankx\Extensions\JankxUX\Builder\Core\Application::getInstance();
}, 1);

// Early init for all contexts (including AJAX)
add_action('init', function() {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        \Jankx\Extensions\JankxUX\Builder\Core\Application::getInstance();
    }
}, 1);

// Add "Edit with UX Builder" button to admin bar
add_action('admin_bar_menu', function($wp_admin_bar) {
    if (!is_admin() && is_singular()) {
        $post = get_post();
        if ($post && current_user_can('edit_post', $post->ID)) {
            $postTypes = jux_builder_post_types();
            if (array_key_exists($post->post_type, $postTypes)) {
                $wp_admin_bar->add_node([
                    'id' => 'jux-builder-edit',
                    'title' => __('Edit with UX Builder', 'jankx'),
                    'href' => jux_builder_edit_link($post->ID),
                    'meta' => [
                        'class' => 'jux-builder-admin-bar',
                    ],
                ]);
            }
        }
    }
}, 999);

// Add "Edit with UX Builder" link to post edit screen
add_action('post_submitbox_start', function() {
    global $post;
    
    if (!$post) return;
    
    $postTypes = jux_builder_post_types();
    if (!array_key_exists($post->post_type, $postTypes)) {
        return;
    }
    
    if (!current_user_can('edit_post', $post->ID)) {
        return;
    }
    ?>
    <div style="margin-bottom: 10px;">
        <a href="<?php echo esc_url(jux_builder_edit_link($post->ID)); ?>" class="button button-hero" style="width: 100%; text-align: center; background: #007cba; color: #fff; border-color: #007cba;">
            <?php _e('Edit with UX Builder', 'jankx'); ?>
        </a>
    </div>
    <?php
});

// Add "Edit with UX Builder" link to page/post list
add_filter('page_row_actions', 'jux_builder_row_actions', 10, 2);
add_filter('post_row_actions', 'jux_builder_row_actions', 10, 2);

if (!function_exists('jux_builder_row_actions')) {
    function jux_builder_row_actions($actions, $post) {
        $postTypes = jux_builder_post_types();
        
        if (array_key_exists($post->post_type, $postTypes) && current_user_can('edit_post', $post->ID)) {
            $actions['jux_builder'] = sprintf(
                '<a href="%s">%s</a>',
                esc_url(jux_builder_edit_link($post->ID)),
                __('UX Builder', 'jankx')
            );
        }
        
        return $actions;
    }
}

// Handle preview mode
add_action('template_redirect', function() {
    if (jux_builder_is_iframe()) {
        // Add builder classes to body for styling
        add_filter('body_class', function($classes) {
            $classes[] = 'jux-builder-preview';
            $classes[] = 'ux-builder-preview'; // For compatibility
            return $classes;
        });
        
        // Add builder wrapper to content
        add_filter('the_content', function($content) {
            return '<div id="jux-builder-content" class="jux-builder-content">' . $content . '</div>';
        }, 999);
    }
});

// Admin styles for "Edit with UX Builder" button
add_action('admin_head', function() {
    ?>
    <style>
    .jux-builder-admin-bar a {
        background-color: #007cba !important;
        color: #fff !important;
    }
    .jux-builder-admin-bar a:hover {
        background-color: #005a87 !important;
    }
    .jux-builder-admin-bar .ab-icon:before {
        content: "\f464";
        top: 2px;
    }
    </style>
    <?php
});

// Load shortcode builder definitions
add_action('jux_builder_setup', function() {
    $shortcodesDir = JANKX_UX_PATH . 'inc/builder/shortcodes';
    
    if (is_dir($shortcodesDir)) {
        foreach (glob($shortcodesDir . '/*.php') as $file) {
            require_once $file;
        }
    }
});
