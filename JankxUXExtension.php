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

        // Initialize Managers (PSR-4 Pattern)
        TemplateManager::init($this);
        UXBlock::register();
        Header::init();
        BuilderManager::init();
        \Jankx\Extensions\JankxUX\Shortcodes\ShortcodeManager::init();

        // Boot legacy constants if needed
        if (!defined('JUX_VERSION')) define('JUX_VERSION', $this->get_version());
        if (!defined('JUX_PATH')) define('JUX_PATH', $this->get_extension_path());

        add_action('template_redirect', [$this, 'handle_preview_mode']);
    }

    public function handle_preview_mode()
    {
        if (isset($_GET['jux-preview']) && $_GET['jux-preview'] == 'true') {
            show_admin_bar(false);
            add_filter('show_admin_bar', '__return_false');
            
            // Allow other components to know we are in preview
            if (!defined('JUX_PREVIEW')) define('JUX_PREVIEW', true);
        }
    }

    public function register_hooks(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        
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
