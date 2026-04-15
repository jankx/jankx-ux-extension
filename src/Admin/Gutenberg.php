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

        $extension = \Jankx\Extensions\JankxUX\JankxUXExtension::get_instance();
        
        wp_enqueue_script(
            'jux-gutenberg-button',
            $extension->get_assets_url() . '/js/jux-gutenberg.js',
            ['wp-edit-post', 'wp-dom-ready', 'wp-element', 'wp-i18n', 'wp-plugins', 'wp-components', 'wp-data', 'wp-compose'],
            $extension->get_version(),
            true
        );

        wp_localize_script('jux-gutenberg-button', 'jankx_ux_gutenberg', [
            'builder_url' => admin_url('admin.php?page=ux-builder&post=' . $post->ID),
            'button_text' => __('Edit with UX Builder', 'jankx'),
        ]);
    }
}
