<?php
namespace Jankx\Extensions\JankxUX;

use Jankx\Extensions\AbstractExtension;
use Jankx\Extensions\JankxUX\PostTypes\UXBlock;
use Jankx\Extensions\JankxUX\Structure\Header;
use Jankx\Extensions\JankxUX\Builder\BuilderManager;
use Jankx\Extensions\JankxUX\TemplateManager;

/**
 * Jankx UX Extension
 * 
 * Professional PSR-4 re-implementation of Flatsome-style UX patterns.
 * 
 * @package Jankx\Extensions\JankxUX
 */
class JankxUXExtension extends AbstractExtension
{
    protected static $instance;

    public function __construct()
    {
        $this->register_autoloader();
        parent::__construct();
    }

    protected function register_autoloader()
    {
        spl_autoload_register(function ($class) {
            $prefix = 'Jankx\\Extensions\\JankxUX\\';
            $base_dir = __DIR__ . '/src/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });

        // Load global functions
        $functions_file = __DIR__ . '/src/global-functions.php';
        if (file_exists($functions_file)) {
            require_once $functions_file;
        }
    }

    public function init(): void
    {
        self::$instance = $this;

        // Define constants first
        $this->define_constants();

        // Load Builder Core (UX Builder replacement)
        $this->load_builder_core();

        // Initialize Managers (PSR-4 Pattern)
        TemplateManager::init($this);
        UXBlock::register();
        Header::init();
        BuilderManager::init();
        \Jankx\Extensions\JankxUX\Shortcodes\ShortcodeManager::init();
    }

    protected function define_constants()
    {
        if (!defined('JANKX_UX_VERSION')) {
            define('JANKX_UX_VERSION', $this->get_version());
        }
        if (!defined('JANKX_UX_PATH')) {
            define('JANKX_UX_PATH', trailingslashit($this->get_extension_path()));
        }
        if (!defined('JANKX_UX_URL')) {
            define('JANKX_UX_URL', trailingslashit($this->get_extension_url()));
        }

        // Legacy compatibility
        if (!defined('JUX_VERSION')) define('JUX_VERSION', JANKX_UX_VERSION);
        if (!defined('JUX_PATH')) define('JUX_PATH', JANKX_UX_PATH);
        if (!defined('JUX_URL')) define('JUX_URL', JANKX_UX_URL);
    }

    protected function load_builder_core()
    {
        // Load Builder Core if available
        $builder_core = $this->get_extension_path() . '/inc/builder/core/jux-builder.php';
        if (file_exists($builder_core)) {
            require_once $builder_core;
        }
    }

    public function handle_preview_mode()
    {
        if (isset($_GET['jux-preview']) && $_GET['jux-preview'] == 'true') {
            show_admin_bar(false);
            add_filter('show_admin_bar', '__return_false');
            
            // Allow other components to know we are in preview
            if (!defined('JUX_PREVIEW')) define('JUX_PREVIEW', true);
            
            // Add builder content container for preview
            add_filter('the_content', function($content) {
                return '<div id="jux-builder-content" class="jux-builder-content">' . $content . '</div>';
            }, 999);
        }
    }

    public function register_hooks(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('template_redirect', [$this, 'handle_preview_mode']);
        
        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
            // Register Admin Logic
            \Jankx\Extensions\JankxUX\Admin\AdminManager::init();
            \Jankx\Extensions\JankxUX\Admin\DashboardStatus::init();
            \Jankx\Extensions\JankxUX\Admin\Gutenberg::init();
        }
    }

    public function enqueue_assets(): void
    {
        wp_enqueue_style('jankx-ux-system', $this->get_assets_url() . '/css/jankx-ux.css', [], $this->get_version());
        
        // Frontend preview helper
        if (isset($_GET['jux-preview'])) {
            wp_enqueue_script('jankx-ux-preview', $this->get_assets_url() . '/js/jux-preview.js', ['jquery', 'jquery-ui-droppable'], $this->get_version(), true);
        }
    }

    public function enqueue_admin_assets($hook)
    {
        if (isset($_GET['page']) && $_GET['page'] === 'ux-builder') {
            wp_enqueue_style('jux-builder-style', $this->get_assets_url() . '/css/jux-builder.css', [], $this->get_version());
            wp_enqueue_script('jux-builder-script', $this->get_assets_url() . '/js/jux-builder.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-droppable', 'backbone', 'underscore'], $this->get_version(), true);
            
            wp_localize_script('jux-builder-script', 'JUX_Builder', array(
                'elements' => BuilderManager::getElements(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'post_id'  => isset($_GET['post']) ? $_GET['post'] : 0,
            ));
        }
    }

    public static function get_instance(): ?self
    {
        return self::$instance;
    }
}
