<?php
namespace Jankx\Extensions\JankxUX\Builder\Core\Ajax;

use Jankx\Extensions\JankxUX\Builder\ElementRegistry;
use Jankx\Extensions\JankxUX\Shortcodes\ShortcodeManager;

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
        add_action('wp_ajax_jux_builder_render_preview', [$this, 'handleRenderPreview']);
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
     * Handle live preview rendering (no page reload)
     * Returns HTML identical to Flatsome frontend
     */
    public function handleRenderPreview()
    {
        check_ajax_referer('jux_builder_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Permission denied');
        }

        $shortcodes = isset($_POST['shortcodes']) ? wp_unslash($_POST['shortcodes']) : [];
        $rendered = [];

        foreach ($shortcodes as $item) {
            $id = isset($item['id']) ? sanitize_text_field($item['id']) : '';
            $tag = isset($item['tag']) ? sanitize_key($item['tag']) : '';
            $atts = isset($item['atts']) ? (array) $item['atts'] : [];
            $content = isset($item['content']) ? wp_kses_post($item['content']) : '';

            // Add builder tracking ID
            $atts['_jux_id'] = $id;

            // Render using ShortcodeManager (identical to frontend)
            $html = ShortcodeManager::render($tag, $atts, $content);

            // Wrap with tracking div for builder
            $html = '<div class="jux-element-wrapper" data-jux-id="' . esc_attr($id) . '" data-tag="' . esc_attr($tag) . '">' . $html . '</div>';

            $rendered[] = [
                'id' => $id,
                'html' => $html,
                'tag' => $tag,
            ];
        }

        wp_send_json_success([
            'rendered' => $rendered,
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
