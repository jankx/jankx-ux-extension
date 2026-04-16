<?php
namespace Jankx\Extensions\JankxUX\Admin;

class Gutenberg
{
    public static function init()
    {
        add_action('enqueue_block_editor_assets', [self::class, 'enqueueAssets']);
    }

    public static function enqueueAssets()
    {
        global $post;
        if (!$post) return;

        // Use WordPress native functions instead of protected extension method
        $assetsUrl = get_template_directory_uri() . '/extensions/jankx-ux/assets';
        $version = '1.0.0'; // Or use filemtime for cache busting

        wp_enqueue_script(
            'jux-gutenberg-button',
            $assetsUrl . '/js/jux-gutenberg.js',
            ['wp-edit-post', 'wp-dom-ready', 'wp-element', 'wp-i18n', 'wp-plugins', 'wp-components', 'wp-data', 'wp-compose'],
            $version,
            true
        );

        wp_localize_script('jux-gutenberg-button', 'jankx_ux_gutenberg', [
            'builder_url' => admin_url('admin.php?page=ux-builder&post=' . $post->ID),
            'button_text' => __('Edit with UX Builder', 'jankx'),
        ]);
    }
}
