<?php
namespace Jankx\Extensions\JankxUX\PostTypes;

class UXBlock
{
    public static function register()
    {
        register_post_type('ux_block', array(
            'labels' => array(
                'name'               => __('UX Blocks', 'jankx'),
                'singular_name'      => __('Block', 'jankx'),
                'add_new'            => __('Add New', 'jankx'),
                'add_new_item'       => __('Add New Block', 'jankx'),
                'edit_item'          => __('Edit Block', 'jankx'),
                'view_item'          => __('View Block', 'jankx'),
                'search_items'       => __('Search Blocks', 'jankx'),
                'not_found'          => __('No Blocks found', 'jankx'),
                'not_found_in_trash' => __('No Blocks found in Trash', 'jankx'),
            ),
            'public'              => true,
            'show_in_menu'        => true,
            'supports'            => array('thumbnail', 'editor', 'title', 'revisions', 'custom-fields'),
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-tagcloud',
        ));

        register_taxonomy('block_categories', array('ux_block'), array(
            'hierarchical'      => true,
            'public'            => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'labels'            => array(
                'name'              => __('Block Categories', 'jankx'),
                'singular_name'     => __('Category', 'jankx'),
            ),
        ));

        add_filter('manage_edit-ux_block_columns', [self::class, 'addColumns']);
        add_action('manage_ux_block_posts_custom_column', [self::class, 'renderColumn'], 10, 2);
    }

    public static function addColumns($columns)
    {
        $new_columns = array();
        foreach ($columns as $key => $title) {
            $new_columns[$key] = $title;
            if ($key == 'title') {
                $new_columns['shortcode'] = __('Shortcode', 'jankx');
            }
        }
        return $new_columns;
    }

    public static function renderColumn($column, $post_id)
    {
        if ($column == 'shortcode') {
            $post = get_post($post_id);
            echo '<textarea readonly style="width:100%; max-height:30px; background:#eee; font-family:monospace; font-size:11px;">[block id="' . $post->post_name . '"]</textarea>';
        }
    }
}
