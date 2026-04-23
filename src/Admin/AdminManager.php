<?php
namespace Jankx\Extensions\JankxUX\Admin;

use Jankx\Extensions\JankxUX\JankxUXExtension;
use Jankx\Extensions\JankxUX\Builder\Core\Application;

class AdminManager
{
    public static function init()
    {
        add_action('admin_menu', [self::class, 'addMenu']);
        add_filter('page_row_actions', [self::class, 'addBuilderLink'], 10, 2);
        add_filter('post_row_actions', [self::class, 'addBuilderLink'], 10, 2);
    }

    public static function addMenu()
    {
        add_submenu_page(
            'edit.php?post_type=ux_block',
            __('UX Builder', 'jankx'),
            __('UX Builder', 'jankx'),
            'edit_pages',
            'ux-builder',
            [self::class, 'renderBuilder']
        );
    }



    public static function renderBuilder()
    {
        $app = Application::getInstance();
        $app->bootstrap();
        $app->initialize(); // This sets up container services like 'editing-post'
        $app->enqueueEditorAssets(); // This populates builder_scripts and builder_styles

        $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
        $post = get_post($post_id);

        jux_get_template_part('admin/builder', 'canvas', [
            'post'    => $post,
            'post_id' => $post_id
        ]);
    }

    public static function addBuilderLink($actions, $post)
    {
        if (in_array($post->post_type, array('ux_block', 'page'))) {
            $url = admin_url('admin.php?page=ux-builder&post=' . $post->ID);
            $actions['ux_builder'] = '<a href="' . esc_url($url) . '" style="font-weight:bold; color:#d26e4b;">' . __('Edit with UX Builder', 'jankx') . '</a>';
        }
        return $actions;
    }
}
