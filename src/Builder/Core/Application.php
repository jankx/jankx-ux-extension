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
    public $builder_scripts = [];
    public $builder_styles = [];

    public function __construct()
    {
        $this->version = JANKX_UX_VERSION ?? '1.0.0';
        $this->container = new Container();
        $this->ajax = new AjaxManager();

        $this->registerServices();
        $this->registerFactories();

        add_action('init', [$this, 'initialize'], 20);
        add_action('wp_enqueue_scripts', [$this, 'enqueuePreviewAssets']);
    }

    /**
     * Render builder template
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Bootstrap the application
     */
    public function bootstrap()
    {
        $this->registerServices();
        $this->registerFactories();
        return $this;
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

            return $container->create(Post\Post::class, [$post]);
        });

        $this->container->service('editing-post', function($container) {
            $editPostId = isset($_GET['edit_post_id']) ? intval($_GET['edit_post_id']) : 0;
            $postId = isset($_GET['post']) ? intval($_GET['post']) : 0;
            $id = $editPostId ? $editPostId : $postId;
            $post = get_post($id);
            
            if (empty($post)) {
                wp_die(__('You attempted to edit an item that doesn\'t exist. Perhaps it was deleted?', 'jankx'));
            }

            return $container->create(Post\Post::class, [$post]);
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
        return (isset($_GET['app']) && $_GET['app'] === 'jux') || (isset($_GET['page']) && $_GET['page'] === 'ux-builder');
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
        // Initialize ElementRegistry to load all elements
        \Jankx\Extensions\JankxUX\Builder\ElementRegistry::init();
        
        $version = $this->version;
        $assetsUrl = JANKX_UX_URL . 'assets/';

        // Core CSS
        wp_enqueue_style(
            'jux-builder-core',
            $assetsUrl . 'css/jux-builder.css',
            ['dashicons', 'forms', 'buttons'],
            $version
        );

        // Core JS (Backbone based) - removed 'moment' as it's not a WP built-in and not needed
        wp_enqueue_script(
            'jux-builder-core',
            $assetsUrl . 'js/jux-builder.js',
            ['jquery', 'jquery-ui-sortable', 'underscore', 'backbone', 'wp-polyfill', 'wp-i18n', 'wp-api-fetch', 'wp-date', 'wp-data'],
            $version,
            true
        );

        // Localize data
        $editingPost = $this->container->resolve('editing-post');
        $currentPost = $this->container->resolve('current-post');

        // Parse content into nodes
        $content = $editingPost->content();
        $parsedNodes = ShortcodeParser::parse($content);

        $data = [
            'postId' => $editingPost->id(),
            'postStatus' => $editingPost->status(),
            'postType' => $editingPost->type(),
            'postTitle' => $editingPost->title(),
            'postContent' => $content,
            'contentNodes' => $parsedNodes,
            'permalink' => $editingPost->permalink(),
            'previewUrl' => add_query_arg('jux-preview', 'true', $editingPost->permalink()),
            'backUrl' => admin_url('post.php?post=' . $currentPost->id() . '&action=edit'),
            'canEdit' => current_user_can('edit_post', $editingPost->id()),
            'canPublish' => current_user_can('publish_post', $editingPost->id()),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jux_builder_nonce'),
            'elements' => \Jankx\Extensions\JankxUX\Builder\ElementRegistry::all(),
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

        // Use wp_localize_script for the main data object - it handles escaping correctly
        wp_localize_script('jux-builder-core', 'juxBuilderData', $data);
        
        // Safety net for wp and moment
        wp_add_inline_script('jux-builder-core', 'window.wp = window.wp || {}; window.moment = window.moment || function() { return { format: function() { return ""; } }; };', 'before');

        // Store the scripts/styles we want to print
        $this->builder_scripts = [
            'wp-polyfill',
            'wp-hooks',
            'wp-i18n',
            'wp-url',
            'wp-api-fetch',
            'wp-date',
            'wp-data',
            'jux-builder-core'
        ];
        $this->builder_styles = ['jux-builder-core', 'dashicons', 'buttons', 'forms'];

        do_action('jux_builder_enqueue_scripts', 'editor');
    }

    /**
     * Enqueue assets for the preview iframe
     */
    public function enqueuePreviewAssets()
    {
        if (!$this->isIframe()) {
            return;
        }

        $version = $this->version;
        $assetsUrl = JANKX_UX_URL . 'assets/';

        // Add the safety net directly to wp_head for the iframe to ensure it's at the very top
        add_action('wp_head', function() {
            echo '<script type="text/javascript">
                window.wp = window.wp || {};
                window.wp.i18n = window.wp.i18n || { setLocaleData: function() {} };
                window.moment = window.moment || function() { return { format: function() { return ""; } }; };
            </script>';
        }, 1);

        wp_enqueue_script(
            'jux-preview',
            $assetsUrl . 'js/jux-preview.js',
            ['jquery', 'wp-polyfill', 'wp-i18n', 'wp-date'],
            $version,
            true
        );

        // Add highlight styles
        add_action('wp_head', function() {
            echo '<style>
                .jux-element-highlight {
                    outline: 2px solid #00a0d2 !important;
                    outline-offset: -2px;
                    position: relative;
                }
                .jux-element-highlight::after {
                    content: "Selected";
                    position: absolute;
                    top: 0;
                    right: 0;
                    background: #00a0d2;
                    color: #fff;
                    font-size: 10px;
                    padding: 2px 5px;
                    z-index: 9999;
                }
            </style>';
        });
    }

    /**
     * Render builder template
     */
    public function render($type, $args = [])
    {
        extract($args);
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

    /**
     * Maybe takeover - placeholder method to prevent fatal error
     * This may be called from cached hooks or old code
     */
    public function maybeTakeover()
    {
        // Placeholder - prevents fatal error from stale hooks
        // TODO: Remove this if the hook registration is found and removed
    }
}
