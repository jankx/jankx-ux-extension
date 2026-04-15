<?php
namespace Jankx\Extensions\JankxUX\Builder\Core;

use Jankx\Extensions\JankxUX\Builder\Core\Services\Container;
use Jankx\Extensions\JankxUX\Builder\Core\Ajax\AjaxManager;
use Jankx\Extensions\JankxUX\Builder\Core\Collections\Elements;
use Jankx\Extensions\JankxUX\Builder\Core\Collections\Components;
use Jankx\Extensions\JankxUX\Builder\Core\Collections\Templates;

/**
 * Builder Application - Main entry point
 * Mimics UxBuilder\Application
 */
class Application
{
    protected static $instance;

    protected $container;
    protected $ajax;
    protected $version;

    public function __construct()
    {
        $this->version = JANKX_UX_VERSION ?? '1.0.0';
        $this->container = new Container();
        $this->ajax = new AjaxManager();

        $this->registerServices();
        $this->registerFactories();

        add_action('init', [$this, 'initialize'], 20);
    }

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register core services
     */
    protected function registerServices()
    {
        $self = $this;

        // App service
        $this->container->service('app', function($container) use ($self) {
            return $self;
        });

        // Templates collection
        $this->container->service('templates', function($container) {
            return $container->create(Templates::class);
        });

        // Components collection
        $this->container->service('components', function($container) {
            return $container->create(Components::class);
        });

        // Elements collection
        $this->container->service('elements', function($container) {
            return $container->create(Elements::class);
        });
    }

    /**
     * Register factories
     */
    protected function registerFactories()
    {
        // Add factories here if needed
    }

    /**
     * Initialize the builder
     */
    public function initialize()
    {
        do_action('jux_builder_setup');

        // Only load editor assets/actions if in builder mode
        if (!$this->isBuilderActive()) {
            return;
        }

        // Register editor-specific services
        $this->container->service('current-post', function($container) {
            $postId = isset($_GET['post']) ? intval($_GET['post']) : 0;
            $post = get_post($postId);
            
            if (empty($post)) {
                wp_die(__('You attempted to edit an item that doesn\'t exist. Perhaps it was deleted?', 'jankx'));
            }

            return $container->create(Post\Post::class, ['post' => $post]);
        });

        $this->container->service('editing-post', function($container) {
            $editPostId = isset($_GET['edit_post_id']) ? intval($_GET['edit_post_id']) : 0;
            $postId = isset($_GET['post']) ? intval($_GET['post']) : 0;
            $id = $editPostId ? $editPostId : $postId;
            $post = get_post($id);
            
            if (empty($post)) {
                wp_die(__('You attempted to edit an item that doesn\'t exist. Perhaps it was deleted?', 'jankx'));
            }

            return $container->create(Post\Post::class, ['post' => $post]);
        });

        // Check permissions
        $editingPost = $this->container->resolve('editing-post');
        if (!current_user_can('edit_post', $editingPost->id())) {
            wp_die(__('Sorry, you are not allowed to edit this item.', 'jankx'));
        }

        // Initialize editor
        $this->initEditor();

        do_action('jux_builder_init');
    }

    /**
     * Initialize editor-specific functionality
     */
    protected function initEditor()
    {
        add_action('current_screen', [$this, 'renderEditor'], 10);
    }

    /**
     * Render the builder editor
     */
    public function renderEditor($screen)
    {
        if ($screen->base !== 'post') {
            return;
        }

        if (!isset($_GET['app']) || !isset($_GET['type'])) {
            return;
        }

        $postTypes = $this->getSupportedPostTypes();
        $post = $this->container->resolve('editing-post')->post();

        if (!array_key_exists($post->post_type, $postTypes)) {
            wp_die(sprintf(__('The <em>%s</em> post type is not available for UX Builder.', 'jankx'), $post->post_type));
        }

        if ($_GET['type'] === 'editor') {
            $this->enqueueEditorAssets();
        }

        $this->render($_GET['type']);
        die;
    }

    /**
     * Check if builder is active
     */
    public function isBuilderActive()
    {
        return isset($_GET['app']) && $_GET['app'] === 'jux';
    }

    /**
     * Check if in builder editor mode
     */
    public function isEditor()
    {
        return $this->isBuilderActive() && isset($_GET['type']) && $_GET['type'] === 'editor';
    }

    /**
     * Check if in builder iframe mode
     */
    public function isIframe()
    {
        return isset($_GET['jux-preview']) && $_GET['jux-preview'] === 'true';
    }

    /**
     * Get supported post types
     */
    public function getSupportedPostTypes()
    {
        return apply_filters('jux_builder_post_types', [
            'page' => 'Pages',
            'post' => 'Posts',
            'ux_block' => 'UX Blocks',
        ]);
    }

    /**
     * Resolve a service from container
     */
    public function resolve($name = null)
    {
        return $this->container->resolve($name);
    }

    /**
     * Enqueue editor assets
     */
    public function enqueueEditorAssets()
    {
        $version = $this->version;
        $assetsUrl = JANKX_UX_URL . 'assets/';

        // Core CSS
        wp_enqueue_style(
            'jux-builder-core',
            $assetsUrl . 'css/jux-builder.css',
            ['dashicons', 'forms', 'buttons'],
            $version
        );

        // Core JS
        wp_enqueue_script(
            'jux-builder-core',
            $assetsUrl . 'js/jux-builder.js',
            ['jquery', 'jquery-ui-sortable', 'underscore'],
            $version,
            true
        );

        // Localize data
        $editingPost = $this->container->resolve('editing-post');
        $currentPost = $this->container->resolve('current-post');

        $data = [
            'postId' => $editingPost->id(),
            'postStatus' => $editingPost->status(),
            'postType' => $editingPost->type(),
            'postTitle' => $editingPost->title(),
            'postContent' => $editingPost->content(),
            'permalink' => $editingPost->permalink(),
            'previewUrl' => add_query_arg('jux-preview', 'true', $editingPost->permalink()),
            'backUrl' => admin_url('post.php?post=' . $currentPost->id() . '&action=edit'),
            'canEdit' => current_user_can('edit_post', $editingPost->id()),
            'canPublish' => current_user_can('publish_post', $editingPost->id()),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jux_builder_nonce'),
            'elements' => $this->container->resolve('elements')->all(),
            'l10n' => [
                'save' => __('Save', 'jankx'),
                'update' => __('Update', 'jankx'),
                'publish' => __('Publish', 'jankx'),
                'preview' => __('Preview', 'jankx'),
                'exit' => __('Exit Builder', 'jankx'),
                'addElement' => __('Add Element', 'jankx'),
                'settings' => __('Settings', 'jankx'),
                'discard' => __('Discard', 'jankx'),
                'apply' => __('Apply', 'jankx'),
            ],
        ];

        wp_localize_script('jux-builder-core', 'juxBuilderData', $data);

        do_action('jux_builder_enqueue_scripts', 'editor');
    }

    /**
     * Render builder template
     */
    public function render($type)
    {
        $template = __DIR__ . '/../../../templates/admin/builder-canvas.php';
        
        if (file_exists($template)) {
            include $template;
        } else {
            wp_die('Builder template not found');
        }
    }

    /**
     * Get version
     */
    public function getVersion()
    {
        return $this->version;
    }
}
