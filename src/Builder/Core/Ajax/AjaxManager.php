<?php
namespace Jankx\Extensions\JankxUX\Builder\Core\Ajax;

/**
 * Ajax Manager for UX Builder
 * Handles all AJAX requests from the builder interface
 */
class AjaxManager
{
    protected $actions = [];

    public function __construct()
    {
        $this->registerHooks();
    }

    protected function registerHooks()
    {
        add_action('wp_ajax_jux_builder_save', [$this, 'handleSave']);
        add_action('wp_ajax_jux_builder_do_shortcode', [$this, 'handleDoShortcode']);
        add_action('wp_ajax_jux_builder_get_elements', [$this, 'handleGetElements']);
        add_action('wp_ajax_jux_builder_copy_as_shortcode', [$this, 'handleCopyAsShortcode']);
    }

    /**
     * Handle saving content
     */
    public function handleSave()
    {
        check_ajax_referer('jux_builder_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Permission denied');
        }

        $postId = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $content = isset($_POST['content']) ? wp_kses_post(stripslashes($_POST['content'])) : '';
        $status = isset($_POST['status']) ? sanitize_key($_POST['status']) : 'draft';

        if (!$postId) {
            wp_send_json_error('Invalid post ID');
        }

        $postData = [
            'ID' => $postId,
            'post_content' => $content,
        ];

        if ($status === 'publish' && current_user_can('publish_posts')) {
            $postData['post_status'] = 'publish';
        }

        $result = wp_update_post($postData, true);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success([
            'post_id' => $postId,
            'permalink' => get_permalink($postId),
            'preview_url' => add_query_arg('jux-preview', 'true', get_permalink($postId)),
        ]);
    }

    /**
     * Handle rendering shortcode
     */
    public function handleDoShortcode()
    {
        check_ajax_referer('jux_builder_nonce', 'nonce');

        $shortcode = isset($_POST['shortcode']) ? wp_kses_post(stripslashes($_POST['shortcode'])) : '';
        
        if (empty($shortcode)) {
            wp_send_json_error('No shortcode provided');
        }

        // Render shortcode
        $rendered = do_shortcode($shortcode);
        
        wp_send_json_success([
            'html' => $rendered,
            'shortcode' => $shortcode,
        ]);
    }

    /**
     * Handle getting element definitions
     */
    public function handleGetElements()
    {
        check_ajax_referer('jux_builder_nonce', 'nonce');

        $elements = apply_filters('jux_builder_elements', []);
        
        wp_send_json_success([
            'elements' => $elements,
        ]);
    }

    /**
     * Handle copying as shortcode
     */
    public function handleCopyAsShortcode()
    {
        check_ajax_referer('jux_builder_nonce', 'nonce');

        $postId = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        if (!$postId) {
            wp_send_json_error('Invalid post ID');
        }

        $post = get_post($postId);
        
        if (!$post) {
            wp_send_json_error('Post not found');
        }

        // Extract shortcodes from content
        $content = $post->post_content;
        
        wp_send_json_success([
            'shortcode' => $content,
        ]);
    }

    /**
     * Register custom AJAX action
     */
    public function registerAction($action, callable $callback)
    {
        $this->actions[$action] = $callback;
        add_action("wp_ajax_jux_builder_{$action}", $callback);
    }
}
