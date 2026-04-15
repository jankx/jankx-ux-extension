<?php
namespace Jankx\Extensions\JankxUX\Tests\Elements;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\JankxUX\Builder\Elements\Text;
use Brain\Monkey;

/**
 * Test suite for Text Element
 */
class TextElementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        
        // Mock WordPress functions
        Monkey\Functions\when('__')->returnArg();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Test element configuration
     */
    public function testElementHasCorrectConfiguration()
    {
        $config = Text::getConfig();
        
        $this->assertEquals('text', $config['tag']);
        $this->assertEquals('Text', $config['name']);
        $this->assertEquals('Content', $config['category']);
        $this->assertEquals('element', $config['type']);
        $this->assertFalse($config['wrap']);
    }

    /**
     * Test element has required options
     */
    public function testElementHasRequiredOptions()
    {
        $config = Text::getConfig();
        $options = $config['options'];
        
        $this->assertArrayHasKey('text', $options);
        $this->assertArrayHasKey('text_align', $options);
        $this->assertArrayHasKey('text_color', $options);
        $this->assertArrayHasKey('font_size', $options);
        $this->assertArrayHasKey('class', $options);
        
        // Check for element name label
        $this->assertArrayHasKey('_label', $options);
    }

    /**
     * Test render with default attributes
     */
    public function testRenderWithDefaults()
    {
        $output = Text::render([], '');
        
        $this->assertStringContainsString('<div', $output);
        $this->assertStringContainsString('class="text-block"', $output);
    }

    /**
     * Test render with text content
     */
    public function testRenderWithTextContent()
    {
        $atts = ['text' => 'Hello World'];
        $output = Text::render($atts, '');
        
        $this->assertStringContainsString('Hello World', $output);
    }

    /**
     * Test render with text align
     */
    public function testRenderWithTextAlign()
    {
        $atts = ['text' => 'Aligned text', 'text_align' => 'center'];
        $output = Text::render($atts, '');
        
        $this->assertStringContainsString('text-align: center', $output);
    }

    /**
     * Test render with text color
     */
    public function testRenderWithTextColor()
    {
        $atts = ['text' => 'Colored text', 'text_color' => '#ff0000'];
        $output = Text::render($atts, '');
        
        $this->assertStringContainsString('color: #ff0000', $output);
    }

    /**
     * Test render with custom class
     */
    public function testRenderWithCustomClass()
    {
        $atts = ['text' => 'Styled text', 'class' => 'my-custom-class'];
        $output = Text::render($atts, '');
        
        $this->assertStringContainsString('my-custom-class', $output);
    }

    /**
     * Test element registration adds to registry
     */
    public function testElementRegistration()
    {
        $registry = \Jankx\Extensions\JankxUX\Builder\ElementRegistry::class;
        
        // Clear registry first
        $registry::clear();
        
        // Register element
        Text::register();
        
        // Verify it's in registry
        $this->assertTrue($registry::has('text'));
        $this->assertEquals('Text', $registry::get('text')['name']);
    }
}
