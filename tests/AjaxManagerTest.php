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
        // Expect add_action to be called for each AJAX endpoint BEFORE creating manager (because of constructor)
        Monkey\Functions\expect('add_action')
            ->atLeast()->once();
        
        $manager = new AjaxManager();
        $manager->registerHooks();
        $this->assertTrue(true);
    }

    /**
     * Test render preview returns correct structure
     */
    public function testRenderPreviewReturnsSuccessResponse()
    {
        $manager = new AjaxManager();
        
        // Mock dependencies
        Monkey\Functions\when('wp_verify_nonce')->justReturn(true);
        Monkey\Functions\when('current_user_can')->justReturn(true);
        Monkey\Functions\when('sanitize_text_field')->returnArg();
        Monkey\Functions\when('sanitize_key')->returnArg();
        Monkey\Functions\when('wp_kses_post')->returnArg();
        Monkey\Functions\when('wp_unslash')->returnArg();

        // Expect wp_send_json_success to be called
        Monkey\Functions\expect('wp_send_json_success')
            ->once();
        
        $manager->handleRenderPreview();
        $this->assertTrue(true);
    }

    /**
     * Test render preview rejects invalid nonce
     */
    public function testRenderPreviewRejectsInvalidNonce()
    {
        $manager = new AjaxManager();
        // Mock dependencies
        Monkey\Functions\when('wp_verify_nonce')->justReturn(false);
        Monkey\Functions\when('sanitize_text_field')->returnArg();

        $_POST['nonce'] = 'invalid_nonce';
        
        // Expect error response
        Monkey\Functions\expect('wp_send_json_error')
            ->once();
        
        $manager->handleRenderPreview();
        $this->assertTrue(true);
    }

    /**
     * Test save content validates permissions
     */
    public function testSaveContentRequiresPermission()
    {
        $manager = new AjaxManager();
        // Mock dependencies
        Monkey\Functions\when('wp_verify_nonce')->justReturn(true);
        Monkey\Functions\when('current_user_can')->justReturn(false);
        Monkey\Functions\when('sanitize_text_field')->returnArg();

        $_POST['nonce'] = 'valid_nonce';
        $_POST['post_id'] = 1;
        
        Monkey\Functions\expect('wp_send_json_error')
            ->with('Permission denied')
            ->once();
        
        $manager->handleSave();
        $this->assertTrue(true);
    }
}
