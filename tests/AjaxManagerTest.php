<?php
namespace Jankx\Extensions\JankxUX\Tests;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\JankxUX\Builder\Core\Ajax\AjaxManager;
use Brain\Monkey;

/**
 * Test suite for AJAX Manager
 */
class AjaxManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Test that AJAX actions are registered on construct
     */
    public function testAjaxActionsAreRegistered()
    {
        $manager = new AjaxManager();
        
        // Expect add_action to be called for each AJAX endpoint
        Monkey\Functions\expect('add_action')
            ->with('wp_ajax_jux_save_content', [$manager, 'handleSave'])
            ->once();
        
        Monkey\Functions\expect('add_action')
            ->with('wp_ajax_jux_builder_do_shortcode', [$manager, 'handleDoShortcode'])
            ->once();
        
        Monkey\Functions\expect('add_action')
            ->with('wp_ajax_jux_builder_get_elements', [$manager, 'handleGetElements'])
            ->once();
        
        Monkey\Functions\expect('add_action')
            ->with('wp_ajax_jux_builder_copy_as_shortcode', [$manager, 'handleCopyAsShortcode'])
            ->once();
        
        Monkey\Functions\expect('add_action')
            ->with('wp_ajax_jux_builder_render_preview', [$manager, 'handleRenderPreview'])
            ->once();
        
        $manager->registerHooks();
    }

    /**
     * Test render preview returns correct structure
     */
    public function testRenderPreviewReturnsSuccessResponse()
    {
        $manager = new AjaxManager();
        
        // Mock nonce verification
        Monkey\Functions\when('wp_verify_nonce')->justReturn(true);
        
        // Mock current_user_can
        Monkey\Functions\when('current_user_can')->justReturn(true);
        
        // Mock POST data
        $_POST['nonce'] = 'valid_nonce';
        $_POST['shortcodes'] = [
            [
                'id' => 'jux-123',
                'tag' => 'text',
                'atts' => [],
                'content' => 'Test content'
            ]
        ];
        
        // Expect wp_send_json_success to be called
        Monkey\Functions\expect('wp_send_json_success')
            ->once();
        
        $manager->handleRenderPreview();
    }

    /**
     * Test render preview rejects invalid nonce
     */
    public function testRenderPreviewRejectsInvalidNonce()
    {
        $manager = new AjaxManager();
        
        // Mock nonce verification to fail
        Monkey\Functions\when('wp_verify_nonce')->justReturn(false);
        
        $_POST['nonce'] = 'invalid_nonce';
        
        // Expect error response
        Monkey\Functions\expect('wp_send_json_error')
            ->once();
        
        $manager->handleRenderPreview();
    }

    /**
     * Test save content validates permissions
     */
    public function testSaveContentRequiresPermission()
    {
        $manager = new AjaxManager();
        
        // Mock nonce verification to pass
        Monkey\Functions\when('wp_verify_nonce')->justReturn(true);
        
        // Mock permission check to fail
        Monkey\Functions\when('current_user_can')->justReturn(false);
        
        $_POST['nonce'] = 'valid_nonce';
        $_POST['post_id'] = 1;
        
        Monkey\Functions\expect('wp_send_json_error')
            ->with('Permission denied')
            ->once();
        
        $manager->handleSave();
    }
}
